<?php

namespace frontend\models;

use common\models\ProductCategory;
use common\models\ProductFilters;
use common\models\ProductFiltersCategory;
use Yii;
use yii\base\Model;
use yii\db\Exception;
use yii\helpers\Inflector;

class CatalogRouter //extends Model
{
    /*** @var string Текущий URL */
    protected $requestUrl;

    /*** @var array Сегменты текущего URL */
    protected $segmentsUrl;

    const PREFIX_SEGMENT_CATALOG_URL = '/catalog';

    /*** Тип страницы: 404 */
    const PAGE_TYPE_404 = '404';

    /*** Тип страницы: раздел каталога */
    const PAGE_TYPE_CATALOG_SECTION = 'CATALOG_SECTION';

    /*** Тип страницы: раздел скидок */
    const PAGE_TYPE_CATALOG_DISCOUNT = 'CATALOG_DISCOUNT';

    /*** Тип страницы: раздел каталога (хабовая страница) */
    const PAGE_TYPE_CATALOG_SECTION_HUB = 'CATALOG_SECTION_HUB';

    /*** Тип страницы: поиск */
    const PAGE_TYPE_CATALOG_SEARCH = 'CATALOG_SEARCH';

    /*** @var array Паттерны сегментов */
    protected $patternUrlSegments = [
        // разделитель пробелов
        'SEPARATOR' => '-',
        // разделитель, если у свойства фильтра несколько значений
        //'MANY' => '-or-',
        'MANY' => '-rrr-',
        // паттерн цены (ot-10000-do-50000)
        'PRICE' => 'price-ot-[###1]-do-[###2]',
        // паттерн цены (ot-10000)
        'PRICE_FROM' => 'price-ot-[###1]',
        // паттерн цены (do-50000)
        'PRICE_TO' => 'price-do-[###1]',
        // паттерн сортировки (/order-price-asc/)
        'SORT' => 'order-[###1]-[###2]',
        // паттерн пагинации (/page-10/)
        'PAGINATION' => 'page-[###1]',
        // паттерн определения цены (ot-10000-do-50000)
        'PRICE_MATCH' => '/^price-ot\-([0-9]+)\-do\-([0-9]+$)/',
        // паттерн определения цены (ot-10000)
        'PRICE_MATCH_FROM' => '/^price-ot\-([0-9]+$)/',
        // паттерн определения цены (to-50000)
        'PRICE_MATCH_TO' => '/^price-do\-([0-9]+$)/',
        // паттерн определения сортировки (/order-price-asc/)
        'SORT_MATCH' => '/^order-([a-zA-Z]+)\-([a-zA-Z]+$)/',
        // паттерн определения пагинации (/page-10/)
        'PAGINATION_MATCH' => '/^page-([0-9]+$)/',
        // паттерн разделения тысячных долей цены (50-000)
        //'PRICE_THOUSANDTHS_DELIMITER' => '-',
        'PRICE_THOUSANDTHS_DELIMITER' => '',
        // паттерн бренда (/brand-kwadron/)
        'BRAND' => 'brand-[###1]',
        // паттерн определения бренда
        'BRAND_MATCH' => '/^brand-([0-9a-zA-Z-_]+$)/',
        // паттерн производителя (/brand-kwadron/)
        'MANUFACTURER' => 'manufacturer-[###1]',
        // паттерн определения производителя
        'MANUFACTURER_MATCH' => '/^manufacturer-([a-zA-Z-]+$)/',
    ];

    /**
     * Получение параметров фильтра для URL.
     *
     * Логика разборки урл :
     *  1. урл бьется на сегменты по символу /
     *  2. выбираются именованные сегменты (pagination, sort, stock)
     *     если найдено, сегменты удаляется.
     *  3. проверятся первый сегмент на 'search' или 'discount' значение.
     *     если найдено, первый сегмент удаляется.
     *  4. проверятся первый сегмент на категорию,
     *     если найдено, первый сегмент удаляется.
     *  5. выбираются именованные сегменты (price, manufacturer, brand)
     *     если найдено, сегменты удаляется.
     *  6. оставшиеся сегменты проверяются на фильтры продуктов,
     *     если найдено, сегменты удаляется.
     *  7. если какие то сегменты еще остались, то возвращается ошибка.
     *
     * @param string $url URL (если не передан, то берется текущий URL страницы)
     * @return array Параметры фильтра для URL
     */
    public function getFilters(string $url = ''): array
    {
        $this->requestUrl = $this->setCurrentRequestUrl($url);
        $this->segmentsUrl = $this->parseRequestUrl($this->requestUrl);

        $filter = [
            'FILTER'    => [],
            'PAGE_TYPE' => self::PAGE_TYPE_404
        ];

        if (count($this->segmentsUrl) === 0) {
            return $filter;
        }

        if ($this->checkOnEmptySegment($this->segmentsUrl)) {
            return $filter;
        }

        if ($this->checkOnDoubleSegment($this->segmentsUrl)) {
            return $filter;
        }

        // pagination segment
        $paginationSegment = $this->getPaginationSegment($this->segmentsUrl);
        if ($paginationSegment['KEY'] !== null) {
            // unset pagination segment
            unset($this->segmentsUrl[$paginationSegment['KEY']]);
        }

        // sort segment
        $sortSegment = $this->getSortSegment($this->segmentsUrl);
        if ($sortSegment['KEY'] !== null) {
            // unset sort segment
            unset($this->segmentsUrl[$sortSegment['KEY']]);
        }

        // stock segment
        $stockSegment = $this->getStockSegment($this->segmentsUrl);
        if ($stockSegment['KEY'] !== null) {
            if ( !$this->checkOnEndPositionStockSegment($stockSegment['KEY'], $this->segmentsUrl)) {
                return $filter;
            }
            // unset stock segment
            unset($this->segmentsUrl[$stockSegment['KEY']]);
        }

        // if first segment is 'search'
        if ($this->segmentsUrl[0] === 'search') {
            $filter['FILTER'] = [];
            $filter['FILTER'][] = $this->getSearchSegment();

            if ($sortSegment['KEY'] !== null) {
                $filter['FILTER'][] = $sortSegment['SEGMENT'];
            }

            $filter['FILTER'][] = $paginationSegment['SEGMENT'];

            $filter['PAGE_TYPE'] = self::PAGE_TYPE_CATALOG_SEARCH;

            array_shift($this->segmentsUrl);
        }
        // if first segment is 'discount'
        elseif ($this->segmentsUrl[0] === 'discount') {
            $filter['FILTER'] = [];
            $filter['FILTER'][] = $this->getDiscountSegment();

            if ($sortSegment['KEY'] !== null) {
                $filter['FILTER'][] = $sortSegment['SEGMENT'];
            }

            $filter['FILTER'][] = $paginationSegment['SEGMENT'];

            $filter['PAGE_TYPE'] = self::PAGE_TYPE_CATALOG_DISCOUNT;

            array_shift($this->segmentsUrl);
        }

        // category segment
        $categorySegment = $this->getCategorySegment($this->segmentsUrl);

        if ($categorySegment['KEY'] !== null) {
            // unset category segment
            $filter['FILTER'][] = $categorySegment['SEGMENT'];
            array_shift($this->segmentsUrl);
        } else {
            if ($filter['PAGE_TYPE'] !== self::PAGE_TYPE_CATALOG_SEARCH || $filter['PAGE_TYPE'] !== self::PAGE_TYPE_CATALOG_DISCOUNT) {
                return $filter;
            }
        }

        // price segment
        $priceSegmentPosition = $this->getPricePosition($this->segmentsUrl);

        if ($priceSegmentPosition !== null) {
            if ( !$this->checkOnEndPositionPriceSegment($priceSegmentPosition, $this->segmentsUrl)) {
                return $filter;
            }

            $priceSegment = $this->segmentsUrl[$priceSegmentPosition];
            // unset price segment
            unset($this->segmentsUrl[$priceSegmentPosition]);
        }

        // manufacturer segment
        do {
            $manufacturerSegmentPosition = $this->getManufacturerPosition($this->segmentsUrl);

            if ($manufacturerSegmentPosition !== null) {
                if (!$this->checkOnEndPositionManufacturerSegment($manufacturerSegmentPosition, $this->segmentsUrl)) {
                    return $filter;
                }

                $manufacturerSegment[] = $this->segmentsUrl[$manufacturerSegmentPosition];
                // unset manufacturer segment
                unset($this->segmentsUrl[$manufacturerSegmentPosition]);
            }
        } while ($manufacturerSegmentPosition !== null);

        // brand segment
        do {
            $brandSegmentPosition = $this->getBrandPosition($this->segmentsUrl);

            if ($brandSegmentPosition !== null) {
                if (!$this->checkOnEndPositionBrandSegment($brandSegmentPosition, $this->segmentsUrl)) {
                    return $filter;
                }

                $brandSegment[] = $this->segmentsUrl[$brandSegmentPosition];
                // unset brand segment
                unset($this->segmentsUrl[$brandSegmentPosition]);
            }
        } while ($brandSegmentPosition !== null);

        // product filters segment
        $propertiesFilter = $this->getFiltersBySegmentsUrl($this->segmentsUrl);

        if ($categorySegment === null &&
            $priceSegment === null &&
            count($brandSegment) === 0 &&
            count($manufacturerSegment) === 0 &&
            count($propertiesFilter) === 0) {
            return $filter;
        }

        if ($this->checkOnNotFoundSegment($propertiesFilter, $this->segmentsUrl)) {
            return $filter;
        }

        if ( !$this->checkOnValidSort($propertiesFilter, $this->segmentsUrl)) {
            return $filter;
        }

        $filter['FILTER'] = array_merge($filter['FILTER'], $propertiesFilter);
        if ($filter['PAGE_TYPE'] !== self::PAGE_TYPE_CATALOG_SEARCH) {
            $filter['PAGE_TYPE'] = $this->getSectionUrlType($propertiesFilter, $this->segmentsUrl);
        }

        if ($filter['PAGE_TYPE'] === self::PAGE_TYPE_404) {
            $filter['FILTER'] = [];

            return $filter;
        }

        if (isset($brandSegment)) {
            $brandFilter = $this->getBrandFilter($brandSegment);

            if (count($brandFilter) > 0) {
                if ($filter['PAGE_TYPE'] === self::PAGE_TYPE_CATALOG_SECTION || $filter['PAGE_TYPE'] === self::PAGE_TYPE_CATALOG_SEARCH) {
                    $filter['FILTER'][] = $brandFilter;
                } else {
                    $filter['FILTER'] = [];
                    $filter['PAGE_TYPE'] = self::PAGE_TYPE_404;

                    return $filter;
                }
            }
        }

        if (isset($manufacturerSegment)) {
            $manufacturerFilter = $this->getManufacturerFilter($manufacturerSegment);

            if (count($manufacturerFilter) > 0) {
                if ($filter['PAGE_TYPE'] === self::PAGE_TYPE_CATALOG_SECTION || $filter['PAGE_TYPE'] === self::PAGE_TYPE_CATALOG_SEARCH) {
                    $filter['FILTER'][] = $manufacturerFilter;
                } else {
                    $filter['FILTER'] = [];
                    $filter['PAGE_TYPE'] = self::PAGE_TYPE_404;

                    return $filter;
                }
            }
        }

        if ($priceSegmentPosition !== null && isset($priceSegment)) {
            $priceFilter = $this->getPriceFilter($priceSegment);

            if (count($priceFilter) > 0) {
                if ($filter['PAGE_TYPE'] === self::PAGE_TYPE_CATALOG_SECTION || $filter['PAGE_TYPE'] === self::PAGE_TYPE_CATALOG_SEARCH) {
                    $filter['FILTER'][] = $priceFilter;
                } else {
                    $filter['FILTER'] = [];
                    $filter['PAGE_TYPE'] = self::PAGE_TYPE_404;

                    return $filter;
                }
            }
        }

        if ($stockSegment['KEY'] !== null) {
            if ($filter['PAGE_TYPE'] === self::PAGE_TYPE_CATALOG_SECTION || $filter['PAGE_TYPE'] === self::PAGE_TYPE_CATALOG_SEARCH) {
                $filter['FILTER'][] = $stockSegment['SEGMENT'];
            } else {
                $filter['FILTER'] = [];
                $filter['PAGE_TYPE'] = self::PAGE_TYPE_404;

                return $filter;
            }
        }

        if ($filter['PAGE_TYPE'] !== self::PAGE_TYPE_CATALOG_SEARCH) {
            if ($sortSegment['KEY'] !== null) {
                $filter['FILTER'][] = $sortSegment['SEGMENT'];
            }

            $filter['FILTER'][] = $paginationSegment['SEGMENT'];


            if (mb_strpos($this->requestUrl, 'q=') && ($filter['PAGE_TYPE'] != self::PAGE_TYPE_CATALOG_SEARCH)) {
                $filter['FILTER'][] = $this->getQSegment();
            }
        }

        $filter['URL_FOR_SEO_META_TAGS'] = $this->getUrlForSeoMetaTags($url);
        //$filter['BREADCRUMBS'] = $this->getBreadcrumbs($filter);

        return $filter;
    }

    /**
     * Генерация URL.
     *
     * @param array $filters Параметры фильтра
     * @param array $segmentsUrl - Если есть возможность передать готовый $segmentsUrl - избавит от запросов к БД
     * @return string URL
     */
    public function generateUrl(array $filters, array $segmentsUrl = []): string
    {
        $url = '';

        if (count($filters) === 0) {
            return $url;
        }

        $filters = $this->keysToLowerCase($filters);

        if (!(bool)$segmentsUrl) {
            $segmentsUrl = $this->getSegmentsUrlByFilters($filters);
        }

        if (isset($filters['category'][0])) {
            if (!empty($filters['category'][0])) {
                $segmentsUrl['category'] = $filters['category'][0];
            }

            unset($filters['category']);
        }

        if (isset($filters['search']) && !empty($filters['search'])) {
            if (is_array($filters['search'])) {
                $searchSegment = $this->generateSearchSegment($filters['search']['Q']);
            } else {
                $searchSegment = $this->generateSearchSegment($filters['search']);
            }

            if ( !empty($searchSegment)) {
                $segmentsUrl['search'] = $searchSegment;

                unset($filters['search']);
            }
        } elseif (isset($filters['is_discount']) && !empty($filters['is_discount'])) {
            if (isset($filters['is_discount']['IS_DISCOUNT']) && $filters['is_discount']['IS_DISCOUNT'] == 'Y') {
                $segmentsUrl['is_discount'] = 'discount';
            }

            unset($filters['is_discount']);
        } else {
            if (!isset($segmentsUrl['category'])) {
                return $url;
            }
        }

        if (isset($filters['brand'])) {
            $brandSegment = $this->generateBrandSegment($filters['brand']);

            if ( !empty($brandSegment)) {
                $segmentsUrl['brand'] = $brandSegment;
            }

            unset($filters['brand']);
        }

        if (isset($filters['manufacturer'])) {
            $manufacturerSegment = $this->generateManufacturerSegment($filters['manufacturer']);

            if ( !empty($manufacturerSegment)) {
                $segmentsUrl['manufacturer'] = $manufacturerSegment;
            }

            unset($filters['manufacturer']);
        }

        if (is_array($filters['price']) && count($filters['price']) > 0) {
            $priceSegment = $this->generatePriceSegment($filters['price']);

            if ( !empty($priceSegment)) {
                $segmentsUrl['price'] = $priceSegment;
            }

            unset($filters['price']);
        }

        if (isset($filters['in_stock'])) {
            if (isset($filters['in_stock']['IN_STOCK']) && $filters['in_stock']['IN_STOCK'] == 'Y') {
                $segmentsUrl['in_stock'] = 'in-stock';
            }

            unset($filters['in_stock']);
        }

        if (isset($filters['sort'])) {
            $sortSegment = $this->generateSortSegment($filters['sort']);

            if ( !empty($sortSegment)) {
                $segmentsUrl['sort'] = $sortSegment;
            }

            unset($filters['sort']);
        }

        if (isset($filters['pagination'])) {
            $paginationSegment = $this->generatePaginationSegment($filters['pagination']);

            if ( !empty($paginationSegment)) {
                $segmentsUrl['pagination'] = $paginationSegment;
            }

            unset($filters['pagination']);
        }

        if (isset($filters['q'])) {
            $qSegment = $this->generateQSegment((string) $filters['q']);

            if ($qSegment !== '') {
                $segmentsUrl['q'] = $qSegment;
            }

            unset($filters['q']);
        }

        if (count($segmentsUrl) > 0) {
            $url = $this->runGenerateUrl($segmentsUrl);
        }

//        if (count($filters) > 0) {
//            $url = '';
//        }

        return $url;
    }

    /**
     * Получение URL для поиска адреса по таблице c мета тегами. Отбрасываются сегменты пагинации, сортировки и параметры запроса.
     *
     * @param string $url URL страницы каталога
     * @return string адаптированный URL
     */
    protected function getUrlForSeoMetaTags(string $url): string
    {
        if (empty($url)) {
            return '';
        }

        $segmentsUrl = [];
        $parsedUrl = parse_url($url, PHP_URL_PATH);

        if (!empty($parsedUrl) && $parsedUrl !== '/') {
            $parsedUrl = ltrim($parsedUrl, '/');
            $parsedUrl = rtrim($parsedUrl, '/');

            $requests = explode('/', $parsedUrl);

            foreach ($requests as $request) {
                $segmentsUrl[] = (string)urldecode($request);
            }
        }

        if (empty($segmentsUrl)) {
            return '';
        }

        $paginationSegment = $this->getPaginationSegment($segmentsUrl);

        if ($paginationSegment['KEY'] !== null) {
            unset($segmentsUrl[$paginationSegment['KEY']]);
        }

        $sortSegment = $this->getSortSegment($segmentsUrl);

        if ($sortSegment['KEY'] !== null) {
            unset($segmentsUrl[$sortSegment['KEY']]);
        }

        $url = self::PREFIX_SEGMENT_CATALOG_URL . '/' . implode('/', $segmentsUrl) . '/';

        return $url;
    }

    /**
     * Добавление сегмента сортировки в URL.
     *
     * @param string $type Тип сортировки
     * @param string $order Направление сортировки
     * @param string $url URL | Текущий URL
     * @return string URL с добавленным сегментом сортировки
     */
    public function addSortSegmentToUrl(string $type, string $order = 'asc', $url = ''): string
    {
        $url = $this->setCurrentRequestUrl($url);
        $segmentsUrl = $this->parseRequestUrl($url);

        $sortSegment = $this->getSortSegment($segmentsUrl);

        if ( !is_null($sortSegment['KEY'])) {
            unset($segmentsUrl[$sortSegment['KEY']]);
        }

        if (
            ($sortSegment['SEGMENT']['PROPERTY_VALUE']['TYPE'] === $type) &&
            ($sortSegment['SEGMENT']['PROPERTY_VALUE']['ORDER'] === $order)
        ) {
            return $url;
        }

        $sortSegment = [
            'TYPE' => $type,
            'ORDER' => $order,
        ];

        $sortSegment = $this->generateSortSegment($sortSegment);

        if ( !empty($sortSegment)) {
            $segmentsUrl[] = $sortSegment;
        }

        $paginationSegment = $this->getPaginationSegment($segmentsUrl);
        $page = $paginationSegment['SEGMENT']['PROPERTY_VALUE']['PAGE'];

        if ( !is_null($paginationSegment['KEY'])) {
            unset($segmentsUrl[$paginationSegment['KEY']]);
        }

        $paginationSegment = [
            'PAGE' => $page,
        ];

        $paginationSegment = $this->generatePaginationSegment($paginationSegment);

        if ( !empty($paginationSegment)) {
            $segmentsUrl[] = $paginationSegment;
        }

        return $this->runGenerateUrl($segmentsUrl);
    }

    /**
     * Добавление сегмента пагинации в URL.
     *
     * @param int $page Номер страницы в постраничной навигации
     * @param string $url URL | Текущий URL
     * @return string URL с добавленным сегментом пагинации
     */
    public function addPaginationSegmentToUrl(int $page, $url = ''): string
    {
        $url = $this->setCurrentRequestUrl($url);
        $segmentsUrl = $this->parseRequestUrlWithoutExplode($url);

        $paginationSegment = $this->getPaginationSegment($segmentsUrl);

        if ( !is_null($paginationSegment['KEY'])) {
            unset($segmentsUrl[$paginationSegment['KEY']]);
        }

        $paginationSegment = [
            'PAGE' => $page,
        ];

        $paginationSegment = $this->generatePaginationSegment($paginationSegment);

        if ( !empty($paginationSegment)) {
            $segmentsUrl[] = $paginationSegment;
        }

        return $this->runGenerateUrl($segmentsUrl);
    }

    /**
     * Получение номера текущей страницы в постраничной навигации.
     *
     * @param string $url URL | Текущий URL
     * @return int Номер текущей страницы в постраничной навигации.
     */
    public function getCurrentPaginationPage($url = ''): int
    {
        $url = $this->setCurrentRequestUrl($url);
        $segmentsUrl = $this->parseRequestUrl($url);

        $paginationSegment = $this->getPaginationSegment($segmentsUrl);

        return $paginationSegment['SEGMENT']['PROPERTY_VALUE']['PAGE'];
    }

    /**
     * Получение строки с GET-параметрами.
     *
     * @return string Строка с GET-параметрами
     */
    public function getQueryParams(): string
    {
        //$queryString = Validator::cleanParam($_SERVER['QUERY_STRING']);
        $queryString = $_SERVER['QUERY_STRING'];

        if (empty($queryString)) {
            return '';
        }

        $queryString = str_replace('&amp;', '&', $queryString);

        return '?' . $queryString;
    }

    /**
     * @return array
     */
    public function getSegmentsUrl()
    {
        return $this->segmentsUrl;
    }

    /**
     * Генерация хлебных крошек на основе фильтра
     *
     * @param array $filter
     * @return array
     */
    protected function getBreadcrumbs(array $filter): array
    {
        $breadcrumbs = $arTemp = array();

        if (isset($filter['FILTER'])) {
            foreach ($filter['FILTER'] as $item) {
                if (isset($item['PROPERTY_ID'])) {
                    $arTemp[$item['PROPERTY_ID']]['URL'][] = $item['PROPERTY_VALUE_CODE'];
                    $arTemp[$item['PROPERTY_ID']]['TITLE'][] = mb_strtolower($item['PROPERTY_VALUE_NAME']);
                }
            }

            if (!empty($arTemp)) {
                $url = '/';
                foreach ($arTemp as $slug) {
                    $url .= implode($this->patternUrlSegments['MANY'], $slug['URL']) . '/';
                    $breadcrumbs[] = array(
                        'URL' => $url,
                        'TITLE' => $this->mb_ucfirst(implode(', ', $slug['TITLE'])),
                    );
                }
            }
        }

        return $breadcrumbs;
    }

    /**
     * Запуск генерации URL.
     *
     * @param array $segmentsUrl Сегменты URL
     * @return string URL
     */
    protected function runGenerateUrl(array $segmentsUrl): string
    {
        $url = '';

        if (count($segmentsUrl) === 0) {
            return $url;
        }

        $uniqueSegments = [];

        if (isset($segmentsUrl['search'])) {
            $url = '/' . trim('search');
        }

        if (isset($segmentsUrl['is_discount'])) {
            $url = '/' . trim('discount');
        }

        if (isset($segmentsUrl['category'])) {
            $category = ProductCategory::getPublishedBySlug(trim($segmentsUrl['category']));
            $segmentsUrl['category'] = \Yii::$app->CategoryList->getUrlSegment($category);

            $url .= '/' . $segmentsUrl['category'];
        }

        foreach ($segmentsUrl as $property => $segment) {

            if ($property === 'q') {
                continue;
            }

            if ($property === 'search') {
                continue;
            }

            if ($property === 'is_discount') {
                continue;
            }

            if ($property === 'category') {
                continue;
            }

            if (is_array($segment) && !empty($segment)) {
                $url .= '/' . implode($this->patternUrlSegments['MANY'], $segment);
                continue;
            }

            if ( !empty($segment) && !isset($uniqueSegments[$segment])) {
                $url .= '/' . trim($segment);
                $uniqueSegments[$segment] = 1;
            }
        }


        // Отбрасываем сегмент сортировки, если он есть (для расчета хабовых страниц)
        if (isset($segmentsUrl['sort'])) {
            unset($segmentsUrl['sort']);
        }

        // Отбрасываем сегмент пагинации, если он есть (для расчета хабовых страниц)
        if (isset($segmentsUrl['pagination'])) {
            unset($segmentsUrl['pagination']);
        }

        if (empty($url)) {
            return $url;
        }

        $url .= '/';

        if (isset($segmentsUrl['search']) && $segmentsUrl['search'] !== '') {
            $url .= $segmentsUrl['search'];
        }

        if (isset($segmentsUrl['q']) && $segmentsUrl['q'] !== '') {
            $url .= $segmentsUrl['q'];
        }

        if (!empty($url)) {
            $url = self::PREFIX_SEGMENT_CATALOG_URL . $url;
        }

        return $url;
    }

    /**
     * Получение сегментов URL.
     *
     * @param string $url URL
     * @return array Сегменты URL
     */
    protected function parseRequestUrl(string $url): array
    {
        $segments = [];

        if (empty($url)) {
            return $segments;
        }

        $parsedUrl = parse_url($url, PHP_URL_PATH);

        if (empty($parsedUrl) || $parsedUrl === '/') {
            return $segments;
        }

        $parsedUrl = ltrim($parsedUrl, '/');
        $parsedUrl = rtrim($parsedUrl, '/');

        $requests = explode('/', $parsedUrl);

        foreach ($requests as $request) {
            if (strpos($request, 'brand-') === 0) {
                $request = str_replace($this->patternUrlSegments['MANY'], $this->patternUrlSegments['MANY'].'brand-', $request);
            }

            if (strpos($request, 'manufacturer-') === 0) {
                $request = str_replace($this->patternUrlSegments['MANY'], $this->patternUrlSegments['MANY'].'manufacturer-', $request);
            }

            $request = explode($this->patternUrlSegments['MANY'], $request);

            foreach ($request as $i => $requestItem) {
                $segments[] = (string) urldecode($requestItem);
            }
        }

        return $segments;
    }

    /**
     * Получение сегментов URL.
     *
     * @param string $url URL
     * @return array Сегменты URL
     */
    protected function parseRequestUrlWithoutExplode(string $url): array
    {
        $segments = [];

        if (empty($url)) {
            return $segments;
        }

        $parsedUrl = parse_url($url, PHP_URL_PATH);

        if (empty($parsedUrl) || $parsedUrl === '/') {
            return $segments;
        }

        $parsedUrl = ltrim($parsedUrl, '/');
        $parsedUrl = rtrim($parsedUrl, '/');

        $requests = explode('/', $parsedUrl);

        foreach ($requests as $request) {
            $segments[] = (string) urldecode($request);
        }

        return $segments;
    }

    public function getOrSegments(): array
    {
        $orSegments = [];
        if (empty($this->requestUrl) || $this->requestUrl === '/') {
            return $orSegments;
        }

        $requestUrl = trim($this->requestUrl, '/');

        $requests = explode('/', $requestUrl);

        foreach ($requests as $request) {
            $request = explode($this->patternUrlSegments['MANY'], $request);

            if (count($request) > 1){
                foreach ($request as $requestItem) {
                    $orSegments[(string) urldecode($requestItem)] = $requestItem;
                }
            }
        }

        return $orSegments;
    }

    /**
     * Получение сегмента категории.
     *
     * @param array $segments Сегменты URL
     * @return array Сегмент категории
     */
    protected function getCategorySegment(array &$segments): array
    {
        $categorySegment = [
            'KEY' => null,
        ];

        if (count($segments) === 0) {
            return $categorySegment;
        }

        $categorySlugPosition = 0;
        if ($slugMarkerPosition = array_search(ProductCategory::LAST_NESTED_SLUG_MARKER, $segments)) {
            $categorySlugPosition = $slugMarkerPosition - 1;
            foreach ($segments as $key => $slug) {
                if ($key <= $slugMarkerPosition) {
                    if ($key != $categorySlugPosition) {
                        unset($segments[$key]);
                    }
                } else break;
            }
        };

        $category = ProductCategory::getPublishedBySlug($segments[$categorySlugPosition]);

        if ($category) {
            $categorySegment = [
                'KEY' => $categorySlugPosition,
                'SEGMENT' => [
                    'PROPERTY_NAME' => 'CATEGORY',
                    'PROPERTY_CODE' => 'CATEGORY',
                    'PROPERTY_VALUE' => [
                        'ID' => $category->id,
                        'MS_ID' => $category->ms_id,
                        'NAME'  => $category->title,
                        'CODE'  => $category->slug,
                        'NESTED_IDS' => \Yii::$app->CategoryList->getNestedIds($category)
                    ],
                ],
            ];
        }

        return $categorySegment;
    }

    /**
     * Получение сегмента пагинации.
     *
     * @param array $segments Сегменты URL
     * @return array Сегмент пагинации
     */
    protected function getPaginationSegment(array $segments): array
    {
        $paginationSegment = [
            'KEY' => null,
            'SEGMENT' => [
                'PROPERTY_NAME'  => 'PAGINATION',
                'PROPERTY_CODE'  => 'PAGINATION',
                'PROPERTY_VALUE' => [
                    'PAGE' => 1,
                ]
            ],
        ];

        if (count($segments) === 0) {
            return $paginationSegment;
        }

        foreach ($segments as $key => $segment) {
            if (preg_match($this->patternUrlSegments['PAGINATION_MATCH'], $segment, $matches)) {
                $pagNumberPage = (int) $matches[1];

                if ($pagNumberPage < 1) {
                    $pagNumberPage = 1;
                }

                $paginationSegment['KEY'] = $key;
                $paginationSegment['SEGMENT']['PROPERTY_VALUE']['PAGE'] = $pagNumberPage;

                break;
            }
        }

        return $paginationSegment;
    }

    /**
     * Получение сегмента товаров в наличии.
     *
     * @param array $segments Сегменты URL
     * @return array Сегмент товаров в наличии
     */
    protected function getStockSegment(array $segments): array
    {
        $stockSegment = [
            'KEY' => null,
            'SEGMENT' => [
                'PROPERTY_NAME'  => 'IN_STOCK',
                'PROPERTY_CODE'  => 'IN_STOCK',
                'PROPERTY_VALUE' => [
                    'IN_STOCK' => 'N',
                ]
            ],
        ];

        if (count($segments) === 0) {
            return $stockSegment;
        }

        foreach ($segments as $key => $segment) {
            if ($segment === 'in-stock') {
                $stockSegment['KEY'] = $key;
                $stockSegment['SEGMENT']['PROPERTY_VALUE']['IN_STOCK'] = 'Y';

                break;
            }
        }

        return $stockSegment;
    }

    /**
     * Получение сегмента поиска.
     *
     * @return array Сегмент поиска
     */
    protected function getSearchSegment(): array
    {
        if (!empty($_REQUEST['q'])) {
            //$q = Validator::cleanParam($_REQUEST['q']);
            $q = $_REQUEST['q'];
        } elseif (!empty($_REQUEST['filter']['q'])) {
            //$q = Validator::cleanParam($_REQUEST['filter']['q']);
            $q = $_REQUEST['filter']['q'];
        } elseif (!empty($_REQUEST['filter']['search']) && is_string($_REQUEST['filter']['search'])) {
            //$q = Validator::cleanParam($_REQUEST['filter']['search']);
            $q = $_REQUEST['filter']['search'];
        } elseif (!empty($_REQUEST['filter']['search']['Q'])) {
            //$q = Validator::cleanParam($_REQUEST['filter']['search']['Q']);
            $q = $_REQUEST['filter']['search']['Q'];
        } else {
            $q = '';
        }

        return [
            'PROPERTY_NAME'  => 'SEARCH',
            'PROPERTY_CODE'  => 'SEARCH',
            'PROPERTY_VALUE' => [
                'Q' => $q,
            ]
        ];
    }

    /**
     * Получение сегмента скидок.
     *
     * @return array Сегмент скидок
     */
    protected function getDiscountSegment(): array
    {
        return [
            'PROPERTY_NAME'  => 'IS_DISCOUNT',
            'PROPERTY_CODE'  => 'IS_DISCOUNT',
            'PROPERTY_VALUE' => [
                'IS_DISCOUNT' => 'Y',
            ]
        ];
    }

    /**
     * Получение сегмента Q.
     *
     * @return array Сегмент Q
     */
    protected function getQSegment(): array
    {
        $query = '';

        if ((bool)$_REQUEST['q']) {
            //$query = Validator::cleanParam($_REQUEST['q']);
            $query = $_REQUEST['q'];
        } elseif ((bool)$_REQUEST['filter']['q']) {
            //$query = Validator::cleanParam($_REQUEST['filter']['q']);
            $query = $_REQUEST['filter']['q'];
        }

        return [
            'PROPERTY_NAME'  => 'Q',
            'PROPERTY_CODE'  => 'Q',
            'PROPERTY_VALUE' => [
                'Q' => $query,
            ],
        ];
    }

    /**
     * Получение сегмента сортировки.
     *
     * @param array $segments Сегменты URL
     * @return array Сегмент сортировки
     */
    protected function getSortSegment(array $segments): array
    {
        $sortSegment = [];

        if (count($segments) === 0) {
            return $sortSegment;
        }

        foreach ($segments as $key => $segment) {
            if (preg_match($this->patternUrlSegments['SORT_MATCH'], $segment, $matches)) {
                $sortSegment = [
                    'KEY' => $key,
                    'SEGMENT' => [
                        'PROPERTY_NAME'  => 'SORT',
                        'PROPERTY_CODE'  => 'SORT',
                        'PROPERTY_VALUE' => [
                            'TYPE'  => (string) $matches[1],
                            'ORDER' => (string) $matches[2],
                        ]
                    ],
                ];

                break;
            }
        }

        return $sortSegment;
    }

    /**
     * Определение типа страницы листинга каталога: хабовая, обычная или 404.
     *
     * @param array $propertiesFilter Фильтр свойств/значений свойств
     * @param array $segments Сегменты URL
     * @return string Тип страницы листинга каталога: хабовая, обычная или 404
     */
    protected function getSectionUrlType(array $propertiesFilter, array $segments): string
    {
        $lastSegment = (string) array_pop($segments);
        $countHubSegments = 0;

        foreach ($propertiesFilter as $key => $property) {
            if ( !isset($property['PROPERTY_VALUE_CODE']) && isset($property['PROPERTY_CODE'])) {
                $countHubSegments ++;
                if ($lastSegment === $property['PROPERTY_CODE']) {
                    $isLastSegmentHub = true;
                }
            }
        }

        /* Нет хабовых сегментов - обычная страница */
        if ($countHubSegments === 0) {
            return self::PAGE_TYPE_CATALOG_SECTION;
        }

        /* Есть хабовые сегменты и последний сегмент в URL хабовый - хабовая страница */
        if ($isLastSegmentHub) {
            return self::PAGE_TYPE_CATALOG_SECTION_HUB;
        }

        /* Есть хабовые сегменты, но последний сегмент в URL НЕ хабовый - 404 страница */
        return self::PAGE_TYPE_404;
    }

    /**
     * Генерация сегмента пагинации для URL.
     *
     * @param array $paginationFilter Фильтр пагинации
     * @return string Сегмент пагинации для URL
     */
    protected function generatePaginationSegment(array $paginationFilter): string
    {
        $currentPage = (int) $paginationFilter['PAGE'];

        if ($currentPage < 2) {
            return '';
        }

        return str_replace('[###1]', $currentPage, $this->patternUrlSegments['PAGINATION']);
    }

    /**
     * Генерация сегмента поиска для URL.
     *
     * @param string $searchFilter Фильтр поиска
     * @return string Сегмент поиска для URL
     */
    protected function generateSearchSegment(string $searchFilter): string
    {
        $searchSegment = '';

        if (empty($searchFilter)) {
            return $searchSegment;
        }

        //$searchSegment = Validator::cleanParam($searchFilter);
        $searchSegment = $searchFilter;

        return ($searchSegment !== '') ? '?q=' . $searchSegment : '';
    }

    /**
     * Генерация сегмента Q для URL.
     *
     * @param string $qParam Фильтр Q
     * @return string Сегмент Q для URL
     */
    public function generateQSegment(string $qParam): string
    {
        $qSegment = '';

        if ($qParam === '') {
            return $qSegment;
        }

        //$qSegment = Validator::cleanParam($qParam);
        $qSegment = $qParam;

        return ($qSegment !== '') ? '?q=' . $qSegment : '';
    }

    /**
     * Генерация сегмента сортировки для URL.
     *
     * @param array $sortFilter Фильтр сортировки
     * @return string Сегмент сортировки для URL
     */
    protected function generateSortSegment(array $sortFilter): string
    {
        $sortSegment = '';

        if (is_array($sortFilter) && (count($sortFilter) === 1)) {
            [$sortField, $sortOrder] = explode('-', reset($sortFilter));
        }

        if ((empty($sortFilter['TYPE']) || empty($sortFilter['ORDER'])) && (empty($sortField) || empty($sortOrder))) {
            return $sortSegment;
        }

        if (empty($sortField) || empty($sortOrder)) {
            $type = (string) $sortFilter['TYPE'];
            $order = ($sortFilter['ORDER'] === 'asc') ? 'asc' : 'desc';
        } else {
            $type = $sortField;
            $order = $sortOrder;
        }


        $sortSegment = str_replace('[###1]', $type, $this->patternUrlSegments['SORT']);
        $sortSegment = str_replace('[###2]', $order, $sortSegment);

        return $sortSegment;
    }

    /**
     * Генерация ценового сегмента URL.
     *
     * @param array $priceFilter Ценовой фильтр
     * @return string Ценовой сегмент URL
     */
    protected function generatePriceSegment(array $priceFilter): string
    {
        $priceSegment = '';

        if (count($priceFilter) === 0) {
            return $priceSegment;
        }

        if ((isset($priceFilter['FROM']) && isset($priceFilter['TO'])) || (is_array($priceFilter) && count($priceFilter) == 2)) {
            $priceFrom = isset($priceFilter['FROM']) ? (int) $priceFilter['FROM'] : (int) $priceFilter[0];
            $priceTo = isset($priceFilter['TO']) ? (int) $priceFilter['TO'] : (int) $priceFilter[1];

            if ($priceFrom < 0 || $priceTo <= 0 || $priceFrom > $priceTo) {
                return $priceSegment;
            }

            $priceFrom = number_format($priceFrom, 0, '', $this->patternUrlSegments['PRICE_THOUSANDTHS_DELIMITER']);
            $priceTo = number_format($priceTo, 0, '', $this->patternUrlSegments['PRICE_THOUSANDTHS_DELIMITER']);

            $priceSegment = str_replace('[###1]', $priceFrom, $this->patternUrlSegments['PRICE']);
            $priceSegment = str_replace('[###2]', $priceTo, $priceSegment);

            return $priceSegment;
        }

        if (isset($priceFilter['FROM'])) {
            $priceFrom = (int) $priceFilter['FROM'];

            if ($priceFrom < 0) {
                return $priceSegment;
            }

            $priceFrom = number_format($priceFrom, 0, '', $this->patternUrlSegments['PRICE_THOUSANDTHS_DELIMITER']);

            $priceSegment = str_replace('[###1]', $priceFrom, $this->patternUrlSegments['PRICE_FROM']);

            return $priceSegment;
        }

        if (isset($priceFilter['TO'])) {
            $priceTo = (int) $priceFilter['TO'];

            if ($priceTo < 0) {
                return $priceSegment;
            }

            $priceTo = number_format($priceTo, 0, '', $this->patternUrlSegments['PRICE_THOUSANDTHS_DELIMITER']);

            $priceSegment = str_replace('[###1]', $priceTo, $this->patternUrlSegments['PRICE_TO']);

            return $priceSegment;
        }

        return $priceSegment;
    }

    /**
     * Генерация сегмента бренда для URL.
     *
     * @param array $brandFilter Фильтр бренда
     * @return string Сегмент бренда для URL
     */
    protected function generateBrandSegment(array $brandFilter): string
    {
        if (empty($brandFilter)) {
            return '';
        }

        $brandValue = implode($this->patternUrlSegments['MANY'], $brandFilter);

        return str_replace('[###1]', $brandValue, $this->patternUrlSegments['BRAND']);
    }

    /**
     * Генерация сегмента производителя для URL.
     *
     * @param array $manufacturerFilter Фильтр производителя
     * @return string Сегмент производителя для URL
     */
    protected function generateManufacturerSegment(array $manufacturerFilter): string
    {
        if (empty($manufacturerFilter)) {
            return '';
        }

        $manufacturerValue = implode($this->patternUrlSegments['MANY'], array_keys($manufacturerFilter));

        return str_replace('[###1]', $manufacturerValue, $this->patternUrlSegments['MANUFACTURER']);
    }

    /**
     * Получение фильтра цены по ценовому сегменту URL.
     *
     * @param string $priceSegment Ценовой сегмент URL
     * @return array Фильтр цены по ценовому сегменту URL
     */
    protected function getPriceFilter(string $priceSegment): array
    {
        $filter = [];

        if (empty($priceSegment)) {
            return $filter;
        }

        if (preg_match($this->patternUrlSegments['PRICE_MATCH'], $priceSegment)) {
            $priceSegment = str_replace('price-ot-', '', $priceSegment);
            $priceSegment = str_replace('-do-', '|', $priceSegment);
            $priceSegment = str_replace($this->patternUrlSegments['PRICE_THOUSANDTHS_DELIMITER'], '', $priceSegment);

            $priceRange = explode('|', $priceSegment);

            $priceFrom = (int) $priceRange[0];
            $priceTo = (int) $priceRange[1];

            if ($priceFrom < 0 || $priceTo <= 0 || $priceFrom > $priceTo) {
                return $filter;
            }

            $filter = [
                'PROPERTY_NAME'  => 'PRICE',
                'PROPERTY_CODE'  => 'PRICE',
                'PROPERTY_VALUE' => [
                    'FROM' => $priceFrom,
                    'TO'   => $priceTo,
                ]
            ];

            return $filter;
        }

        if (preg_match($this->patternUrlSegments['PRICE_MATCH_FROM'], $priceSegment)) {
            $priceSegment = str_replace(['price-ot-', '-'], '', $priceSegment);

            $priceFrom = (int) $priceSegment;

            if ($priceFrom < 0) {
                return $filter;
            }

            $filter = [
                'PROPERTY_NAME'  => 'PRICE',
                'PROPERTY_CODE'  => 'PRICE',
                'PROPERTY_VALUE' => [
                    'FROM' => $priceFrom,
                ]
            ];

            return $filter;
        }

        if (preg_match($this->patternUrlSegments['PRICE_MATCH_TO'], $priceSegment)) {
            $priceSegment = str_replace(['price-do-', '-'], '', $priceSegment);

            $priceTo = (int) $priceSegment;

            if ($priceTo <= 0) {
                return $filter;
            }

            $filter = [
                'PROPERTY_NAME'  => 'PRICE',
                'PROPERTY_CODE'  => 'PRICE',
                'PROPERTY_VALUE' => [
                    'TO' => $priceTo,
                ]
            ];

            return $filter;
        }

        return $filter;
    }

    /**
     * Получение фильтра бренда по сегменту URL.
     *
     * @param array $segment сегмент URL
     * @return array Фильтр бренда по сегменту URL
     */
    protected function getBrandFilter(array $segment): array
    {
        $filter = [];

        if (!is_array($segment) && empty($segment)) {
            return $filter;
        }

        $brandValues = array();
        foreach ($segment as $item) {
            if (preg_match($this->patternUrlSegments['BRAND_MATCH'], $item)) {
                $item = str_replace('brand-', '', $item);

                $brandValues[] = $item;
            }
        }

        if (count($brandValues)) {
            $filter = [
                'PROPERTY_NAME' => 'BRAND',
                'PROPERTY_CODE' => 'BRAND',
                'PROPERTY_VALUE' => $brandValues,
            ];
        }

        return $filter;
    }

    /**
     * Получение фильтра производителя по сегменту URL.
     *
     * @param array $segment сегмент URL
     * @return array Фильтр производителя по сегменту URL
     */
    protected function getManufacturerFilter(array $segment): array
    {
        $filter = [];

        if (!is_array($segment) && empty($segment)) {
            return $filter;
        }

        $manufacturerValues = array();
        foreach ($segment as $item) {
            if (preg_match($this->patternUrlSegments['MANUFACTURER_MATCH'], $item)) {
                $item = str_replace('manufacturer-', '', $item);

                $manufacturerValues[] = $item;
            }
        }

        if (count($manufacturerValues)) {
            $values = $this->getManufacturerValues($manufacturerValues);

            $arTemp = array();
            foreach ($values as $slug => $value) {
                if (in_array($slug, $manufacturerValues)) {
                    $arTemp[$slug] = $value;
                }
            }

            if (!empty($arTemp)) {
                $filter = [
                    'PROPERTY_NAME' => 'MANUFACTURER',
                    'PROPERTY_CODE' => 'MANUFACTURER',
                    'PROPERTY_VALUE' => $arTemp,
                ];
            }
        }

        return $filter;
    }

    /**
     * Получение позиции ценового сегмента среди сегментов URL.
     *
     * @param array $segments Сегменты URL
     * @return null|int $position null - если нет ценового сегмента среди сегментов URL | int - позиция ценового
     * сегмента среди сегментов URL
     */
    protected function getPricePosition(array $segments)
    {
        $position = null;

        foreach ($segments as $key => $segment) {
            if (preg_match($this->patternUrlSegments['PRICE_MATCH'], $segment)) {
                $position = $key;

                break;
            }

            if (preg_match($this->patternUrlSegments['PRICE_MATCH_FROM'], $segment)) {
                $position = $key;

                break;
            }

            if (preg_match($this->patternUrlSegments['PRICE_MATCH_TO'], $segment)) {
                $position = $key;

                break;
            }
        }

        return $position;
    }

    /**
     * Получение позиции сегмента бренда среди сегментов URL.
     *
     * @param array $segments Сегменты URL
     * @return null|int $position null - если нет сегмента бренда среди сегментов URL | int - позиция
     * сегмента бренда среди сегментов URL
     */
    protected function getBrandPosition(array $segments)
    {
        $position = null;

        foreach ($segments as $key => $segment) { // таких сегментов может быть несколько (через -or-)
            if (preg_match($this->patternUrlSegments['BRAND_MATCH'], $segment)) {
                $position = $key;

                //break;
            }
        }

        return $position;
    }

    /**
     * Получение позиции сегмента производителя среди сегментов URL.
     *
     * @param array $segments Сегменты URL
     * @return null|int $position null - если нет сегмента производителя среди сегментов URL | int - позиция
     * сегмента производителя среди сегментов URL
     */
    protected function getManufacturerPosition(array $segments)
    {
        $position = null;

        foreach ($segments as $key => $segment) { // таких сегментов может быть несколько (через -or-)
            if (preg_match($this->patternUrlSegments['MANUFACTURER_MATCH'], $segment)) {
                $position = $key;

                //break;
            }
        }

        return $position;
    }

    /**
     * Проверка позиций сегментов URL на правильную сортировку.
     *
     * @param array $propertiesFilter Фильтр свойств/значений свойств
     * @param array $segments Сегменты URL
     * @return bool true - все сегменты в URL находятся на правильных позициях | false - сегменты в URL находятся на не
     * правильных позициях
     */
    protected function checkOnValidSort(array $propertiesFilter, array $segments): bool
    {
        $isValidSort = true;

        $sortedProperties = [];
        $segments = array_values($segments);

        foreach ($propertiesFilter as $property) {
            if (isset($property['PROPERTY_VALUE_CODE'])) {
                $sortedProperties[] = $property['PROPERTY_VALUE_CODE'];

                continue;
            }

            if (isset($property['PROPERTY_CODE'])) {
                $sortedProperties[] = $property['PROPERTY_CODE'];
            }
        }

        $sortedProperties = array_unique($sortedProperties);
        $sortedProperties = array_values($sortedProperties);

        foreach ($segments as $i => $segment) {
            if ($segment !== $sortedProperties[$i]) {
                $isValidSort = false;

                break;
            }
        }

        return $isValidSort;
    }

    /**
     * Проверка: есть ли среди сегментов URL - сегмент, который не является свойством/значением свойства массива
     * фильтрации.
     *
     * @param array $propertiesFilter Фильтр свойств/значений свойств
     * @param array $segments Сегменты URL
     * @return bool true - есть сегмент, который не является свойством/значением свойства массива фильтрации | false -
     * нет сегмента, который не является свойством/значением свойства массива фильтрации
     */
    protected function checkOnNotFoundSegment(array $propertiesFilter, array $segments): bool
    {
        $isNotFound = false;

        $segmentsFlipped = array_flip($segments);

        foreach ($propertiesFilter as $property) {
            if (isset($property['PROPERTY_CODE']) && isset($segmentsFlipped[$property['PROPERTY_CODE']])) {
                unset($segmentsFlipped[$property['PROPERTY_CODE']]);
            }

            if (isset($property['PROPERTY_VALUE_CODE']) && isset($segmentsFlipped[$property['PROPERTY_VALUE_CODE']])) {
                unset($segmentsFlipped[$property['PROPERTY_VALUE_CODE']]);
            }
        }

        if (count($segmentsFlipped) > 0) {
            $isNotFound = true;
        }

        return $isNotFound;
    }

    /**
     * Проверка: является ли позиция сегмента товара в наличии - последней в сегментах URL.
     *
     * @param int $position Позиция сегмента товара в наличии в сегментах URL
     * @param array $segments Сегменты URL
     * @return bool true - сегмент товара в наличии - послендий в сегментах URL | false - сегмент товара в наличии - не послендий в
     * сегментах URL
     */
    protected function checkOnEndPositionStockSegment(int $position, array $segments): bool
    {
        $isEndPosition = false;

        $endSegmentsPosition = count($segments) - 1;

        if ($position === $endSegmentsPosition) {
            $isEndPosition = true;
        }

        return $isEndPosition;
    }

    /**
     * Проверка: является ли позиция ценнового сегмента - последней в сегментах URL.
     *
     * @param int $position Позиция ценового сегмента в сегментах URL
     * @param array $segments Сегменты URL
     * @return bool true - ценовой сегмент - послендий в сегментах URL | false - ценовой сегмент - не послендий в
     * сегментах URL
     */
    protected function checkOnEndPositionPriceSegment(int $position, array $segments): bool
    {
        $isEndPosition = false;

        $endSegmentsPosition = count($segments) - 1;

        if ($position === $endSegmentsPosition) {
            $isEndPosition = true;
        }

        return $isEndPosition;
    }

    /**
     * Проверка: является ли позиция сегмента - последней в сегментах URL.
     *
     * @param int $position Позиция сегмента в сегментах URL
     * @param array $segments Сегменты URL
     * @return bool true - сегмент - послендий в сегментах URL | false - сегмент - не послендий в
     * сегментах URL
     */
    protected function checkOnEndPositionBrandSegment(int $position, array $segments): bool
    {
        $isEndPosition = false;

        $endSegmentsPosition = count($segments) - 1;

        if ($position === $endSegmentsPosition) {
            $isEndPosition = true;
        }

        return $isEndPosition;
    }

    /**
     * Проверка: является ли позиция сегмента - последней в сегментах URL.
     *
     * @param int $position Позиция сегмента в сегментах URL
     * @param array $segments Сегменты URL
     * @return bool true - сегмент - послендий в сегментах URL | false - сегмент - не послендий в
     * сегментах URL
     */
    protected function checkOnEndPositionManufacturerSegment(int $position, array $segments): bool
    {
        $isEndPosition = false;

        $endSegmentsPosition = count($segments) - 1;

        if ($position === $endSegmentsPosition) {
            $isEndPosition = true;
        }

        return $isEndPosition;
    }

    /**
     * Проверка на дублирование сегмента в сегментах URL.
     *
     * @param array $segments Сегменты URL
     * @return bool true - среди сегментов URL существует дублируемый сегмент | false - среди сегментов URL нет
     * дублируемого сегмента
     */
    protected function checkOnDoubleSegment(array $segments): bool
    {
        $isDoubleSegment = false;
        $segmentsUrl = [];

        foreach ($segments as $key => $segment) {
            if ( !isset($segmentsUrl[$segment])) {
                $segmentsUrl[$segment] = 'Y';

                continue;
            }

            $isDoubleSegment = true;

            break;
        }

        return $isDoubleSegment;
    }

    /**
     * Проверка на существование пустого сегмента в сегментах URL.
     *
     * @param array $segments Сегменты URL
     * @return bool true - среди сегментов URL существует пустой сегмент | false - среди сегментов URL нет пустого
     * сегмента
     */
    protected function checkOnEmptySegment(array $segments): bool
    {
        $isEmptySegment = false;

        foreach ($segments as $segment) {
            if ($segment === '') {
                $isEmptySegment = true;

                break;
            }
        }

        return $isEmptySegment;
    }

    /**
     * Установка текущего URL.
     *
     * @param string $url URL | Текущий URL страницы, если $url - пустой
     * @return string Текущий URL
     */
    protected function setCurrentRequestUrl(string $url = ''): string
    {
        $cleanedUrl = !empty($url) ? $url : $_SERVER['REQUEST_URI'];

        //return Validator::cleanParam($cleanedUrl);
        return $cleanedUrl;
    }

    /**
     * Перевод ключей фильтра в нижний регистр.
     *
     * @param array $filters Фильтр
     * @return array Фильтр с ключами в нижнем регистре
     */
    protected function keysToLowerCase(array $filters): array
    {
        foreach ($filters as $key => $segments) {
            $lowerKey = trim(mb_strtolower($key));

            if ($lowerKey !== $key) {
                $filters[$lowerKey] = $segments;

                unset($filters[$key]);
            }
        }

        return $filters;
    }

    /**
     * Функция преобразует первый символ мультибайтовой строки в верхний регистр
     *
     * @param $string
     * @param $encoding
     * @return string
     */
    private function mb_ucfirst(string $string, string $encoding = "utf8"): string
    {
        $firstChar = mb_substr($string, 0, 1, $encoding);
        $then = mb_substr($string, 1, null, $encoding);

        return mb_strtoupper($firstChar, $encoding) . $then;
    }

    /**
     * Получение параметров фильтра по сегментам URL из БД.
     *
     * @param array $segmentsUrl Сегменты URL
     * @return array Параметры фильтра
     */
    protected function getFiltersBySegmentsUrl(array $segmentsUrl): array
    {
        $filters = [];

        if (count($segmentsUrl) === 0) {
            return $filters;
        }

        $propertyValues = $this->getPropertyValues($segmentsUrl);
        $properties = $this->getProperties($propertyValues, $segmentsUrl);

        if (count($properties) === 0) {
            return [];
        }

        foreach ($properties as $property) {
            $foundPropertyValue = false;

            foreach ($propertyValues as $propertyValue) {
                if ($property['ID'] === $propertyValue['PROPERTY_ID']) {
                    $filters[] = [
                        'PROPERTY_ID' => $property['ID'],
                        'PROPERTY_NAME' => $property['NAME'],
                        'PROPERTY_CODE' => $property['CODE'],
                        //'PROPERTY_FOR' => $this->getBindingPropertyToIndex($property['UF_CODE']),
                        'PROPERTY_SORT' => $property['ID'],
                        'PROPERTY_VALUE_ID' => $propertyValue['ID'],
                        'PROPERTY_VALUE_NAME' => $propertyValue['NAME'],
                        'PROPERTY_VALUE_CODE' => $propertyValue['CODE'],
                        'PROPERTY_VALUE_SORT' => $propertyValue['SORT'],
                    ];

                    $foundPropertyValue = true;
                }
            }

            if ( !$foundPropertyValue) {
                $filters[] = [
                    'PROPERTY_ID' => $property['ID'],
                    'PROPERTY_NAME' => $property['NAME'],
                    'PROPERTY_CODE' => $property['CODE'],
                    //'PROPERTY_FOR' => $this->getBindingPropertyToIndex($property['UF_CODE']),
                    'PROPERTY_SORT' => $property['SORT'],
                ];
            }
        }

        return $filters;
    }

    /**
     * Получение сегментов URL по параметрам фильтра из БД.
     *
     * @param array $filters Параметры фильтра
     * @return array Сегменты URL
     */
    protected function getSegmentsUrlByFilters(array $filters): array
    {
        $segmentsUrl = [];

        if (count($filters) === 0) {
            return $segmentsUrl;
        }

        $unUsedFilters = [
            'search',
            'category',
            'price',
            'brand',
            'in_stock',
            'is_discount',
            'sort',
            'pagination',
            'q',
        ];

        $cleanProperties = [];
        $cleanPropertyValues = [];

        foreach ($filters as $propertyName => $property) {
            if (in_array($propertyName, $unUsedFilters, true)) {
                continue;
            }

            $cleanProperties[] = $propertyName;

            foreach ($property as $i => $propertyValue) {
                if ($propertyValue !== '') {
                    $cleanPropertyValues[] = $propertyValue;
                }
            }
        }

        if (count($cleanProperties) === 0) {
            return $segmentsUrl;
        }

        $propertyValues = ProductFilters::find()
            ->select([
                ProductFilters::tableName() . '.id',
                ProductFilters::tableName() . '.category_id',
                ProductFilters::tableName() . '.slug',
            ])
            ->where(['in', ProductFilters::tableName() . '.id', $cleanPropertyValues])
            ->andWhere([ProductFilters::tableName() . '.status' => 1])
            ->orderBy([ProductFilters::tableName() . '.category_id' => SORT_ASC, ProductFilters::tableName() . '.sort' => SORT_ASC])
            ->asArray()
            ->all();

        if (count($propertyValues)) {
            foreach ($propertyValues as $item) {
                $segmentsUrl[$item['category_id']][] = $item['slug'];
            }
        }

        //echo '<pre>';print_r($segmentsUrl);echo '<pre>';die();

        return $segmentsUrl;
    }

    /**
     * Получение свойств по сегментам URL.
     *
     * @param array $propertyValues Значения свойств по сегментам URL
     * @param array $segments Сегменты URL
     * @return array Свойства по сегментам URL
     */
    protected function getProperties(array $propertyValues, array $segments): array
    {
        $propertyValuesId = [];

        if (count($propertyValues) > 0) {
            $propertyValuesSegments = array_unique(array_column($propertyValues, 'CODE'));
            $propertyValuesId = array_unique(array_column($propertyValues, 'PROPERTY_ID'));

            $propertyValuesSegmentsFlipped = array_flip($propertyValuesSegments);

            foreach ($segments as $i => $segment) {
                if (isset($propertyValuesSegmentsFlipped[$segment])) {
                    unset($segments[$i]);
                }
            }
        }

        $properties = ProductFiltersCategory::find()
            ->select([
                ProductFiltersCategory::tableName() . '.id ID',
                ProductFiltersCategory::tableName() . '.title NAME',
                ProductFiltersCategory::tableName() . '.slug CODE',
            ])
            ->where(['in', ProductFiltersCategory::tableName() . '.id', $propertyValuesId])
            ->andWhere([ProductFiltersCategory::tableName() . '.status' => 1])
            ->orderBy([ProductFiltersCategory::tableName() . '.id' => SORT_ASC])
            ->asArray()
            ->all();

        return $properties;
    }

    /**
     * Получение значений свойств по сегментам URL.
     *
     * @param array $segments Сегменты URL
     * @return array Значения свойств по сегментам URL
     */
    protected function getPropertyValues(array $segments): array
    {
        $propertyValues = [];

        if (count($segments) === 0) {
            return $propertyValues;
        }

        $propertyValues = ProductFilters::find()
            ->select([
                ProductFilters::tableName() . '.id ID',
                ProductFilters::tableName() . '.category_id PROPERTY_ID',
                ProductFilters::tableName() . '.title NAME',
                ProductFilters::tableName() . '.slug CODE',
                ProductFilters::tableName() . '.sort SORT',
            ])
            ->where(['in', ProductFilters::tableName() . '.slug', $segments])
            ->andWhere([ProductFilters::tableName() . '.status' => 1])
            ->orderBy([ProductFilters::tableName() . '.sort' => SORT_ASC])
            ->asArray()
            ->all();

        return $propertyValues;
    }

    /**
     * Получение значений производителя по его сегменту URL.
     *
     * @param array $slugs Сегмент URL
     * @return array Значения производителя по его сегменту URL
     */
    protected function getManufacturerValues(array $slugs): array
    {
        $manufacturerValues = [];

        if (count($slugs) === 0) {
            return $manufacturerValues;
        }

        $values = Product::find()
            ->select(['manufacturer'])
            ->distinct()
            ->orderBy(['manufacturer' => SORT_ASC])
            ->asArray()
            ->all();

        if (!empty($values)) {
            foreach ($values as $value) {
                if (!empty($value['manufacturer'])) {
                    $slug = Inflector::slug($value['manufacturer']);
                    $manufacturerValues[$slug] = $value['manufacturer'];
                }
            }
        }

        return $manufacturerValues;
    }



//    protected function getBindingPropertyToIndex(string $code): int
//    {
//        $result = 0;
//
//        if (CModule::IncludeModule('iblock')) {
//            $properties = CIBlockProperty::GetList(
//                Array("sort" => "asc", "name" => "asc"),
//                Array("IBLOCK_ID" => IBLOCK_ID__CATALOG, "ACTIVE" => "Y")
//            );
//            $arProps = array();
//            while ($prop_fields = $properties->GetNext()) {
//                $arProps[$prop_fields["ID"]] = $prop_fields["CODE"];
//            }
//
//            foreach ($arProps as $id => $prop) {
//                if (strtoupper($code) == $prop) {
//                    return $id;
//                }
//            }
//        }
//
//        return $result;
//    }
}
