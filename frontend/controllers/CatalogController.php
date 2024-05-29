<?php

namespace frontend\controllers;

use common\models\ProductFiltersProduct;
use common\models\ProductPrice;
use common\models\Reviews;
use common\models\SeoMetaTags;
use common\models\UserClientOrder;
use common\models\UserClientOrder_Product;
use frontend\models\ReviewForm;
use frontend\models\UserGuestClient;
use yii\data\ActiveDataProvider;
use yii\db\QueryInterface;
use yii\web\NotFoundHttpException;

use common\models\ProductCategory;
use frontend\models\CatalogRouter;
use frontend\models\Product;
use Yii;
use yii\web\Response;

class CatalogController extends BaseController
{

    public $productPageSize = 24;
    const  OHL_SREDSTVA_ID = 'eb87d71c-babf-11ea-0a80-07b80009c5b4';

    /**
     * @return string
     */
    public function actionIndex()
    {
        return $this->render('index', [
            'dataProvider' => new ActiveDataProvider([
                'query' => ProductCategory::find()->published()->order(),
                'pagination' => ['pageSize' => 100],
            ]),
            'productsNew' => new ActiveDataProvider([
                'query' => Product::find()->new()->limit(16),
                'pagination' => false,
            ]),
            'productsSale' => new ActiveDataProvider([
                'query' => Product::find()->sale()->limit(16),
                'pagination' => false,
            ]),
            'productsPopular' => new ActiveDataProvider([
                'query' => Product::find()->popular()->limit(16),
                'pagination' => false,
            ]),
        ]);
    }

    public function actionAddReview()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        if (!!Yii::$app->user->isGuest) {
            return [
                'success' => false,
                'msg' => 'Отзывы могут писать только зарегистрированные пользователи. Пожалуйста, пройдите <a href="/lk/login/">регистрацию</a>'
            ];
        }

        if (Yii::$app->request->isAjax && !Yii::$app->request->isPjax){
            $model = new ReviewForm();
            $post = Yii::$app->request->post();

            if ($model->load($post)) {
                if ($model->validate()) {
                    $review = new Reviews();
                    $review->product_id = $post['ReviewForm']['product_id'];
                    $review->user_client_id = Yii::$app->user->id;
                    $review->is_published = 0;
                    $review->rating = $post['ReviewForm']['rating'];
                    //$review->date = time();
                    $review->date = date('d.m.Y H:i:s');
                    $review->text = $post['ReviewForm']['text'];

                    if ($review->save()) {
                        return [
                            'success' => true,
                            'msg' => 'Спасибо, ваш отзыв отправлен на модерацию'
                        ];
                    }
                } else {
                    return [
                        'success' => false,
                        'msg' => 'Поставьте оценку и напишите текст отзыва'
                    ];
                }
            }

            return [
                'success' => false,
                'msg' => 'Ошибка при добавлении отзыва'
            ];
        }
    }

    public static function getSeoMetaTags($url)
    {
        $result = array(
            'url' => $url,
        );

        if (!empty($url)) {
            $metaTags = SeoMetaTags::find()
                ->where([SeoMetaTags::tableName() . '.url' => $url])
                ->one();

            if ($metaTags) {
                $result = array(
                    'h1' => $metaTags->h1,
                    'title' => $metaTags->seo_title,
                    'description' => $metaTags->seo_desc,
                    'keywords' => $metaTags->seo_keywords,
                    'seo_text' => $metaTags->seo_text,
                    'url' => $url,
                );
            }
        }

        return $result;
    }

    public function actionGenerateUrl() {
        $filters = Yii::$app->request->post('filters');

        if (!Yii::$app->request->isAjax) {
            throw new NotFoundHttpException();
        }

        Yii::$app->response->format = Response::FORMAT_JSON;

        $url = '';
        $metaTags = array();
        if ($filters) {
            $router = new CatalogRouter();
            $url = $router->generateUrl($filters);

            $urlForSeo = $url;
            if (!empty($urlForSeo)) {
                // Отбросить параметр поиска 'q'
                if (strpos($urlForSeo, '?q=') !== false) {
                    $arr = explode('?q=', $urlForSeo);
                    $urlForSeo = $arr[0];
                }

                $urlForSeo = ltrim($urlForSeo, '/');
                $urlForSeo = rtrim($urlForSeo, '/');
                $arr = explode('/', $urlForSeo);

                $segments = array();
                foreach ($arr as $segment) {
                    if (strpos($segment, 'page-') === 0) { // сегмент пагинации, если содержится, начиная с первого символа
                        continue;
                    }

                    if (strpos($segment, 'order-') === 0) { // сегмент сортировки, если содержится, начиная с первого символа
                        continue;
                    }

                    $segments[] = $segment;
                }
                if (!empty($segments)) {
                    $metaTags = $this->getSeoMetaTags('/' . implode('/', $segments) . '/');
                }
            }
        }

        return array(
            'uri' => $url,
            'metaTags' => $metaTags,
        );
    }

    public function actionRouting($slug)
    {
        $router = new CatalogRouter();
        $arSegment = explode('/', $slug);
        //\Yii::debug($arSegment);

        if (isset($_REQUEST['q']) && !empty($_REQUEST['q'])) {
            if ($arSegment[0] !== 'search') {
                $slug = 'search/'.$slug;
                $arSegment = explode('/', $slug);
            }

            $slug .= '/?q=' . $_REQUEST['q'];
        }

        $arFilter = $router->getFilters($slug);
        //\Yii::debug($arFilter);

        if (isset($arFilter['PAGE_TYPE'])) {
            switch ($arFilter['PAGE_TYPE']) {
                case CatalogRouter::PAGE_TYPE_404:
                    if (count($arSegment) === 2) {
                        return $this->actionProduct($arSegment[1]);
                    }

                    break;
                case CatalogRouter::PAGE_TYPE_CATALOG_SECTION:
                case CatalogRouter::PAGE_TYPE_CATALOG_SEARCH:
                case CatalogRouter::PAGE_TYPE_CATALOG_DISCOUNT:

                    return $this->catalogFilter($arFilter);
            }
        }

        throw new NotFoundHttpException;
    }

    public function catalogFilter($arFilter)
    {
        $arValues = array();
        foreach ($arFilter['FILTER'] as $filter) {
            if (isset($filter['PROPERTY_ID']) && $filter['PROPERTY_VALUE_ID']) {
                $arValues['filters'][$filter['PROPERTY_ID']][] = $filter['PROPERTY_VALUE_ID'];
                continue;
            }

            switch ($filter['PROPERTY_CODE']) {
                case 'IS_DISCOUNT':
                    if ($filter['PROPERTY_VALUE']['IS_DISCOUNT'] === 'Y') {
                        $arValues['is_discount'] = 1;
                    }

                    break;

                case 'CATEGORY':
                    if ($filter['PROPERTY_VALUE']['MS_ID']) {
                        $arValues['category'] = $filter['PROPERTY_VALUE']['MS_ID'];
                        if (!empty($filter['PROPERTY_VALUE']['NESTED_IDS'])) {
                            $arValues['category_nested_ids'] = $filter['PROPERTY_VALUE']['NESTED_IDS'];
                        }
                    }

                    break;
                case 'BRAND':
                    if (is_array($filter['PROPERTY_VALUE']) && !empty($filter['PROPERTY_VALUE'])) {
                        $arValues['brand'] = $filter['PROPERTY_VALUE'];
                    }

                    break;
                case 'MANUFACTURER':
                    if (is_array($filter['PROPERTY_VALUE']) && !empty($filter['PROPERTY_VALUE'])) {
                        $arValues['manufacturer'] = $filter['PROPERTY_VALUE'];
                    }

                    break;
                case 'PRICE':
                    if (isset($filter['PROPERTY_VALUE']['FROM']) || isset($filter['PROPERTY_VALUE']['TO'])) {
                        $arValues['prices'][] = $filter['PROPERTY_VALUE']['FROM'] ?: 0;
                        $arValues['prices'][] = $filter['PROPERTY_VALUE']['TO'] ?: 500000;
                    }

                    break;
                case 'IN_STOCK':
                    if ($filter['PROPERTY_VALUE']['IN_STOCK'] === 'Y') {
                        $arValues['inStock'] = 1;
                    }

                    break;
                case 'SORT':
                    if ($filter['PROPERTY_VALUE']['TYPE'] && $filter['PROPERTY_VALUE']['ORDER']) {
                        $arValues['sorted'] = $filter['PROPERTY_VALUE'];
                    }

                    break;
                case 'PAGINATION':
                    if ($filter['PROPERTY_VALUE']['PAGE']) {
                        $arValues['pagePost'] = $filter['PROPERTY_VALUE']['PAGE'] ?: 1;
                    }

                    break;
                case 'SEARCH':
                    if ($filter['PROPERTY_VALUE']['Q']) {
                        $arValues['q'] = $filter['PROPERTY_VALUE']['Q'];
                    }

                    break;
            }
        }

        if (empty($arValues)) {
            throw new NotFoundHttpException;
        }

        if (!isset($arValues['prices'])) {
            $arValues['prices'] = array(0, 500000);
        }

        if (!isset($arValues['pagePost'])) {
            $arValues['pagePost'] = 1;
        }

        $profile = Yii::$app->user->identity->userProfile;
        //\Yii::debug($arValues);

        $minMaxPrices = array(
            'min' => 0,
            'max' => 500000,
        );

        if ($arValues['category'] == self::OHL_SREDSTVA_ID && (!!Yii::$app->user->isGuest || (empty($profile->sale_ms_id) && empty($profile->sale_brands)))) {
            // Показать раздел пустым
            $productDataProvider = new ActiveDataProvider([
                'query' => Product::find()
                    ->cache(100000)
                    ->andWhere(['=', 1, 0])->order()
            ]);
        } else {
            if ($arValues['q'] || !empty($arValues['brand']) || !empty($arValues['manufacturer'])) {
                $product_query = Product::find()
                    ->distinct()
                    ->select(['config_ms_id']);

                if ($arValues['q']) {
                    $product_query->search($arValues['q']);
                }

                if (!empty($arValues['brand'])) {
                    $product_query->andFilterWhere(['in', Product::tableName() . '.brand_id', $arValues['brand']]);
                }

                if (!empty($arValues['manufacturer'])) {
                    $product_query->andFilterWhere(['in', Product::tableName() . '.manufacturer', $arValues['manufacturer']]);
                }
            }

            // Product DataProvider
            $productDataProvider = new ActiveDataProvider([
                'query' => Product::find()
                    ->distinct()
                    ->preparePrice()
                    ->andFilterWhere([
                        'between',
                        'if(p2.price, p2.price, p1.price)',
                        $arValues['prices'][0] * 100,
                        $arValues['prices'][1] * 100
                    ]),
                'pagination' => [
                    'pageSize' => $this->productPageSize,
                    'page' => $arValues['pagePost'] - 1
                ]
            ]);

            // add category filter
            if (isset($arValues['category'])) {
                if (isset($arValues['category_nested_ids'])) {
                    $catIds = $arValues['category_nested_ids'];
                    array_push($catIds, $arValues['category']);
                } else $catIds = $arValues['category'];

                $productDataProvider->query->andFilterWhere([
                    'in',
                    Product::tableName() . '.category_ms_id',
                    $catIds
                ]);
            }

            if (isset($arValues['filters']) && is_array($arValues['filters'])) {
                foreach ($arValues['filters'] as $filter) {
                    $productDataProvider->query->andWhere([
                        'in',
                        Product::tableName() . '.id',
                        ProductFiltersProduct::find()
                            ->select('{{%product_filters_product}}.product_id AS id')
                            ->where(['in', '{{%product_filters_product}}.product_filters_id', $filter])
                    ]);
                }
            } else {
                $productDataProvider->query->prepareConfig(false);
            }

            if (isset($product_query)) {
                $productDataProvider->query->andFilterWhere(['in', 'config_ms_id', $product_query]);
            }

            if ($arValues['inStock'] == 1) {
                $productDataProvider->query->andWhere(['>', Product::tableName() . '.amount', 0]);
            }

            // add discount filter
            if ($arValues['is_discount'] == 1) {
                // массив id товаров со скидкой
                // todo: just add is_discount to main query ???
                $arDiscount = Product::find()
                    ->cache(7200)
                    ->select(Product::tableName() . '.config_ms_id')
                    ->andWhere([Product::tableName() . '.is_discount' => 1])
                    ->andWhere(['<>', Product::tableName() . '.status', 0])
                    ->andWhere(['<>', Product::tableName() . '.is_ms_deleted', 1])
                    ->all();

                $discount = array();
                foreach ($arDiscount as $config) {
                    $discount[] = $config->config_ms_id;
                }
                $discount = array_unique($discount);

                $productDataProvider->query->andWhere([
                    'in',
                    Product::tableName() . '.config_ms_id',
                    $discount
                ]);
            }

            $productDataProvider->query
                ->andWhere(['<>', Product::tableName() . '.status', 0])
                ->andWhere(['<>', Product::tableName() . '.is_ms_deleted', 1])
                ->groupBy([Product::tableName() . '.config_ms_id']);

            $defaultOrder = [
                'if(' . Product::tableName() . '.amount > 0, 1, 0)' => SORT_DESC,
                Product::tableName() . '.view_count' => SORT_DESC,
            ];

            if (isset($arValues['sorted'])) {
                switch ($arValues['sorted']['TYPE'] . '-' . $arValues['sorted']['ORDER']) {
                    case 'brand-desc':
                        $order = [Product::tableName() . '.brand_id' => SORT_DESC];
                        break;
                    case 'title-asc':
                        $order = [Product::tableName() . '.title' => SORT_ASC];
                        break;
                    case 'views-desc':
                        $order = [Product::tableName() . '.view_count' => SORT_DESC];
                        break;
                    case 'price-desc':
                        $order = ['if(p2.price, p2.price, p1.price)' => SORT_DESC];
                        break;
                    case 'price-asc':
                        $order = ['if(p2.price, p2.price, p1.price)' => SORT_ASC];
                        break;
                    default:
                        $order = [
                            'if(' . Product::tableName() . '.amount > 0, 1, 0)' => SORT_DESC,
                            'if(p2.price, p2.price, p1.price)' => SORT_ASC
                        ];
                }
            } else {
                $order = $defaultOrder;
            }

            if (!Yii::$app->request->isAjax) {
                $minMaxPrices = $this->getMinMaxPrices($productDataProvider->query);
            }

            if (isset($order)) {
                $productDataProvider->query->orderBy($order);
            }
        }

        \Yii::debug($productDataProvider->query->createCommand()->getRawSql());

        if (Yii::$app->request->isAjax && !Yii::$app->request->isPjax) {
            return $this->renderPartial('/catalog/_product-list', [
                'isAjax' => true,
                'productDataProvider' => $productDataProvider,
                'emptyListShow' => true,
                'hasFilter' => !empty($filters) && is_array($filters),
                'brandPage' => false,
            ]);
        }

        $metaTags = $this->getSeoMetaTags($arFilter['URL_FOR_SEO_META_TAGS']);

        return $this->render('category', [
            'category' => $arValues['category'] ? ProductCategory::getPublishedByMsId($arValues['category']) : null,
            'productDataProvider' => $productDataProvider,
            'minMaxPrices' => $minMaxPrices,
            'discount' => $arValues['is_discount'] == 1 ? 1 : 0,
            'arFilter' => $arValues,
            'metaTags' => $metaTags,
        ]);
    }

    /**
     * Получает значения минимальной и максимальной цены для слайдера в фильтре.
     *
     * @param QueryInterface $query
     * @return array
     */
    protected function getMinMaxPrices($query)
    {
        $result = array(
            'min' => 0,
            'max' => 500000,
        );

        if ($query) {
            $min_prod = $query->orderBy(['if(p2.price, p2.price, p1.price)' => SORT_ASC])->one();
            $max_prod = $query->orderBy(['if(p2.price, p2.price, p1.price)' => SORT_DESC])->one();

            $min = !is_null($min_prod) ? $min_prod->clientPriceValue / 100 : 0;
            $max = !is_null($max_prod) ? $max_prod->clientPriceValue / 100 : 500000;
            if ($min == $max) {
                $min = 0;
            }

            $result = array(
                'min' => $min,
                'max' => $max,
            );
        }

        return $result;
    }

    /**
     * @param  $slugProduct
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionProduct($slugProduct = null)
    {
        $model = Product::find()
            ->preparePrice()
            ->published()->andWhere([Product::tableName() . '.slug' => $slugProduct])->one();

        if (!$model) {
            throw new NotFoundHttpException;
        }

        $model->incViewCount();

        // Выборка аналогов
        if ($model->similar) {
            $similarQuery = Product::find()->similar(explode(PHP_EOL, $model->similar))->limit(16);
        } else {
            $similarQuery = Product::find()->category($model->category ? $model->category->ms_id : null)->limit(16);
        }

        return $this->render('product', [
            'model' => $model,
            'productsBuy' => new ActiveDataProvider([
                'query' => Product::find()
                    ->buy($model->id)->limit(16),
                'pagination' => false,
            ]),
            'productsCat' => new ActiveDataProvider([
                'query' => $similarQuery,
                'pagination' => false,
            ]),
            'productsPopular' => new ActiveDataProvider([
                'query' => Product::find()->popular()->limit(16),
                'pagination' => false,
            ]),
            'productsSale' => new ActiveDataProvider([
                'query' => Product::find()->sale()->limit(16),
                'pagination' => false,
            ]),
        ]);
    }

    /**
     * @param  $id
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionConfig($id)
    {
        $model = Product::find()->cache(7200)->where(['id' => $id])->one();

        if (!$model) {
            throw new NotFoundHttpException;
        }

        if (!Yii::$app->request->isAjax || Yii::$app->request->isPjax) {
            throw new NotFoundHttpException;
        }

        $products = $model->configs;
        $products_count = count($products);

        if ($products_count <= 3) {
            $products_left = $products;
            $products_right = [];
        } elseif ($products_count <= 5) {
            $products_left = array_slice($products, 0, $products_count - 1);
            $products_right = array_slice($products, -1, 1);
        } elseif ($products_count == 6) {
            $products_left = array_slice($products, 0, $products_count - 2);
            $products_right = array_slice($products, -2, 2);
        } else {
            $products_left = array_slice($products, 0, ceil($products_count / 2 + 1));
            $products_right = array_slice($products, ceil($products_count / 2 + 1), floor($products_count / 2 - 1));
        }

        return $this->renderPartial('_config', [
            'id' => $id,
            'products_left' => $products_left,
            'products_right' => $products_right,
            'count' => $products_count
        ]);
    }

    protected function getClient()
    {
        return Yii::$app->client->identity;
    }

    public function actionDeferred($type, $id)
    {
        if (!Yii::$app->request->isAjax) {
            throw new NotFoundHttpException();
        }
        $this->getClient()->changeDeferred($type, $id);
    }

    public function actionGetCart()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $data = [];

        $cart = $this->getClient()->getCart();

        // collect data from guest cart
        if (Yii::$app->user->isGuest) {
            /** @var UserGuestClient $cart */
            $data['order_id'] = session_id();
            $data['total_count'] = (int) $cart->count;
            $data['total_price'] = ProductPrice::formatPrice($cart->sumTotal);
            $data['discount'] = 0;

            if (!empty($cart->linkProducts)) {
                foreach ($cart->linkProducts as $link) {
                    $data['products'][] = [
                        'id' => $link->product->id,
                        'slug' => $link->product->slug,
                        'title' => $link->product->title,
                        'count' => (int) $link->count,
                        'price' => ProductPrice::formatPrice($link->product->clientPriceValue),
                    ];
                }
            }
        } else { // collect data from client cart
            /** @var UserClientOrder $cart */
            $discount = $cart->sum_discount ?: 0;
            $data['order_id'] = $cart->id;
            $data['total_count'] = $cart->getCount();
            $data['total_price'] = ProductPrice::formatPrice($cart->getSum());
            $data['discount'] = $discount;

            $productLinks = $cart->getLinkProducts()->with('product')->all();
            foreach ($productLinks as $link) {
                $data['products'][] = [
                    'id' => $link->product->id,
                    'slug' => $link->product->slug,
                    'title' => $link->product->title,
                    'count' => $link->count,
                    'price' => ProductPrice::formatPrice($link->price),
                ];
            }
        }

        return $data;
    }

    /**
     * @param int $count
     * @param int $id
     * @return bool
     * @throws NotFoundHttpException
     */
    public function actionAddCart($count, $id)
    {
        if (!$p = Product::findOne($id)) {
            throw new NotFoundHttpException();
        }

        if (!Yii::$app->request->isAjax) {
            throw new NotFoundHttpException();
        }

        $cart = $this->getClient()->getCart();

        foreach ((array)$cart as $l) {
            // check if remaining product count not lower than requested count
            // taking into account previously added count for this product in linkProducts
            if ($l->id == $id && $p->amount - $l->count < $count) {
                return false;
            }
        }

        if (!Yii::$app->user->isGuest) {
            // при добавлениие товара в корзину сбрасывать купон
            $cart->sum_discount = null;
            $cart->coupon_id = null;
            $cart->save(false);
        }

        Yii::$app->response->format = Response::FORMAT_JSON;

        return $this->getClient()->addCart($count, $id);
    }

    public function actionChangeCart($count, $id, $coupon_code = '')
    {
        $p = Product::findOne($id);

        if (!$p) {
            throw new NotFoundHttpException();
        }

        $cart = Yii::$app->client->identity->getCart();

        foreach ($cart->linkProducts as $l) {
            if ($l->id == $id && $p->amount < $count) {
                return false;
            }
        }

        if (!Yii::$app->request->isAjax) {
            throw new NotFoundHttpException();
        }
        Yii::$app->response->format = Response::FORMAT_JSON;

        return $this->getClient()->changeCart($count, $id, $coupon_code);
    }

    public function actionRemoveCart($id, $coupon_code = '')
    {
        if (!Yii::$app->request->isAjax) {
            throw new NotFoundHttpException();
        }

        Yii::$app->response->format = Response::FORMAT_JSON;

        return $this->getClient()->removeCart($id, $coupon_code);
    }

    public function actionAddCartConfigs()
    {
        if (!Yii::$app->request->isAjax) {
            throw new NotFoundHttpException();
        }

        Yii::$app->response->format = Response::FORMAT_JSON;

        return $this->getClient()->addCartConfigs(Yii::$app->request->post('configs'));
    }

    public function actionAddProductsEcommerce()
    {
        if (!Yii::$app->request->isAjax) {
            throw new NotFoundHttpException();
        }

        Yii::$app->response->format = Response::FORMAT_JSON;

        $configs = Yii::$app->request->post('configs');

        $response = false;
        if (is_array($configs) && !empty($configs)) {
            $products = Product::find()->preparePrice()->where(['in', Product::tableName() . '.id', array_keys($configs)])->all();

            if (!empty($products)) {
                $response = array(
                    'ecommerce' => array(
                        'currencyCode' => 'RUB'
                    )
                );

                foreach ($products as $product) {
                    $response['ecommerce']['add']['products'][] = array(
                        'id' => $product->id,
                        'name' => $product->title,
                        'price' => floatval($product->clientPriceValue / 100),
                        'brand' => $product->brand_->title ?: '',
                        'category' => $product->category->title,
                        'quantity' => (int)$configs[$product->id]['count'],
                    );
                }
            }
        }

        return $response;
    }

    public function actionRemoveProductsEcommerce($count, $id)
    {
        if (!Yii::$app->request->isAjax) {
            throw new NotFoundHttpException();
        }

        Yii::$app->response->format = Response::FORMAT_JSON;

        $id = intval($id);
        $count = intval($count);
        $response = false;
        if ($id > 0 && $count > 0) {
            $product = Product::find()->preparePrice()->where([Product::tableName() . '.id' => $id])->one();

            $response = array(
                'ecommerce' => array(
                    'currencyCode' => 'RUB',
                    'remove' => array(
                        'products' => array(
                            'id' => $product->id,
                            'name' => $product->title,
                            'price' => floatval($product->clientPriceValue / 100),
                            'brand' => $product->brand_->title ?: '',
                            'category' => $product->category->title,
                            'quantity' => $count,
                        )
                    )
                )
            );
        }

        return $response;
    }

    public function actionChangeProductsEcommerce($count, $id)
    {
        if (!Yii::$app->request->isAjax) {
            throw new NotFoundHttpException();
        }

        Yii::$app->response->format = Response::FORMAT_JSON;

        $id = intval($id);
        $count = intval($count);
        $response = false;
        if ($id > 0 && $count > 0) {
            $product = Product::find()->preparePrice()->where([Product::tableName() . '.id' => $id])->one();

            $cart = Yii::$app->client->identity->getCart();
            $position = UserClientOrder_Product::findOne(['order_id' => $cart->id, 'product_id' => $id]);
            $pos_count = 0;

            //echo '<pre>';print_r($position);echo '</pre>';die();
            if ($position) {
                $pos_count = $position->count;
            }

            if ($pos_count > 0) {
                if ($count > $pos_count) {
                    $response = array(
                        'ecommerce' => array(
                            'currencyCode' => 'RUB',
                            'add' => array(
                                'products' => array(
                                    'id' => $product->id,
                                    'name' => $product->title,
                                    'price' => floatval($product->clientPriceValue / 100),
                                    'brand' => $product->brand_->title ?: '',
                                    'category' => $product->category->title,
                                    'quantity' => $count - $pos_count,
                                )
                            )
                        )
                    );
                } elseif ($count < $pos_count) {
                    $response = array(
                        'ecommerce' => array(
                            'currencyCode' => 'RUB',
                            'remove' => array(
                                'products' => array(
                                    'id' => $product->id,
                                    'name' => $product->title,
                                    'price' => floatval($product->clientPriceValue / 100),
                                    'brand' => $product->brand_->title ?: '',
                                    'category' => $product->category->title,
                                    'quantity' => $pos_count - $count,
                                )
                            )
                        )
                    );
                }
            }
        }

        return $response;
    }
}
