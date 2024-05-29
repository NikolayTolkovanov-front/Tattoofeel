<?php

namespace common\components;

use common\models\ProductCategory;
use common\models\ProductFiltersCategory;

use yii\base\Component;
use yii\helpers\Url;

class CategoryList extends Component
{
    private $includeFilters = false;

    private function _getParentChain(ProductCategory $model)
    {
        $SQL = <<<SELECT
select
    id, parent_id, ms_id, level, slug, title,  @parent_id
from (select * from tt_product_category order by level desc, id desc) as cat_sorted,
     (select @parent_id := :parentId) as init
where
    find_in_set(id, @parent_id) and
    if(parent_id, length(@parent_id := concat(@parent_id, ',', parent_id)), 1);
SELECT;

        $connection = \Yii::$app->getDb();
        $command = $connection->createCommand($SQL, [
            ':parentId' => $model->parent_id
        ]);

        return $command->queryAll();
    }

    private function _getNestedChain(ProductCategory $model)
    {
        $SQL = <<<SELECT
select
    id, parent_id, ms_id, level, slug, title,  @parent_id
from (select * from tt_product_category order by level asc, id asc) as cat_sorted,
     (select @parent_id := :parentId) as init
where
    find_in_set(parent_id, @parent_id) and
    length(@parent_id := concat(@parent_id, ',', id));
SELECT;

        $connection = \Yii::$app->getDb();
        $command = $connection->createCommand($SQL, [
            ':parentId' => $model->id
        ]);

        return $command->queryAll();
    }

    private function _getNestedList()
    {
//        \Yii::$app->dbCache->flush();

        $nestedList = [];

        $list = ProductCategory::find()->published()->all();

        foreach ($list as $category) {
            $filters = [];
            if ($this->includeFilters) {
                $filtersTableName = ProductFiltersCategory::tableName();
                $junctionTableName = '{{%product_filters_category_product_category}}';
                $filterCats = $category->getProductFiltersCategories()
                    ->select("$filtersTableName.*, junction.visible_in_menu as visible_in_menu")
                    ->leftJoin("$junctionTableName as junction",
                        "$filtersTableName.id = junction.product_filters_category_id AND junction.product_category_id = {$category->id}")
                    ->all()
                ;
                if (count($filterCats)) {
                    foreach ($filterCats as $_filter_cat) {
                        if (!$_filter_cat->visible_in_menu) continue;
                        $filterValues = $_filter_cat->getPubFiltersByCatId($category->ms_id);
                        if (count($filterValues)) {
                            foreach ($filterValues as $_filter_value) {
                                $filters[] = [
                                    'slug' => $_filter_value['slug'],
                                    'title' => $_filter_value['title'],
                                ];
                            }
                        }
                    }
                }
            }

            $nestedList[$category->id] = [
                'id' => $category->id,
                'parent_id' => $category->parent_id,
                'level' => $category->level,
                'title' => $category->title,
                'slug' => $category->slug,
                'icon' => $category->getIconUrl(),
                'filters' => $filters,
                'nested' => [],
            ];
        }

        $maxLevel = ProductCategory::find()->max('level');
        if ($maxLevel) {
            for ($currentLevel = $maxLevel; $currentLevel > 0; $currentLevel--) {
                foreach ($nestedList as $category) {
                    if ($category['level'] == $currentLevel) {
                        if (array_key_exists($category['parent_id'], $nestedList)) {
                            $nestedList[$category['parent_id']]['nested'][] = $category;
                            unset($nestedList[$category['id']]);
                        }
                    }
                }
            }
        }

        return $nestedList;
    }

    public function getForSelection()
    {
        function fillOptions($list): array
        {
            $options = ['' => 'не выбрано'];

            foreach ($list as $category) {
                $tab = str_repeat(' - ', $category['level']);
                $options[$category['id']] = $tab . $category['title'];
                if (!empty($category['nested'])) {
                    $options += fillOptions($category['nested']);
                }
            }

            return $options;
        }

        $nestedList = $this->_getNestedList();

        return fillOptions($nestedList);
    }

    public function getForMenu($options = [])
    {
        function fillMenu($list, $options = []): string
        {
            $menu = '<ul%s>%s</ul>';
            $itemTemplate =
            '<li class="my-menu-item">'.
                '<div class="my-menu-link">'.
                    '<a href="%s">'.
                        '<span class="my-menu-img" style="background-image:url(%s)"></span> %s'.
                    '</a>'.
                    '<svg width="18" height="12" viewBox="0 0 18 12" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M2 2L9 10L16 2" stroke="#363636" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>'.
                '</div>%s</li>';
                
            $items = '';

            if (!empty($options['parentSlug'])) {
                $parentSlug = $options['parentSlug'];
            } else $parentSlug = '';

            foreach ($list as $category) {
                $slug = $parentSlug . $category['slug'] . '/';

                // sub-menu
                if (!empty($category['nested'])) {
                    $subMenu = fillMenu($category['nested'], [
                        'parentSlug' => $slug
                    ]);
                } else $subMenu = '';

                // filters
                if (!empty($category['filters'])) {
                    $filtersMenu = fillFilters($category['filters'], [
                        'parentSlug' => $slug
                    ]);
                } else $filtersMenu = '';

                $nestedCategoryMarker = $category['parent_id'] ? ProductCategory::LAST_NESTED_SLUG_MARKER . '/' : '';
                $url = Url::to(['/catalog/' . $slug . $nestedCategoryMarker]);
                $items .= sprintf($itemTemplate, $url, $category['icon'], $category['title'], $subMenu . $filtersMenu);
            }

            if (!empty($options['menuClass'])) {
                $menuClass = ' class="'. $options['menuClass'] .'"';
            } else $menuClass = '';

            return sprintf($menu, $menuClass, $items);
        }

        function fillFilters($list, $options = []): string
        {
            $menu = '<ul%s>%s</ul>';
            $itemTemplate =
            '<li class="my-filter-item">'.
                '<div class="my-filter-link">'.
                    '<a href="%s">'.
                        '<span class="my-filter-img" style="background-image:url(%s)"></span> %s'.
                    '</a>'.
                '</div>%s</li>';
            $items = '';

            if (!empty($options['parentSlug'])) {
                $parentSlug = $options['parentSlug'];
            } else $parentSlug = '';

            foreach ($list as $filter) {
                $slug = $parentSlug . ProductCategory::LAST_NESTED_SLUG_MARKER .'/'. $filter['slug'] .'/';
                $url = Url::to(['/catalog/' . $slug]);
                $items .= sprintf($itemTemplate, $url, '', $filter['title'], '');
            }

            if (!empty($options['menuClass'])) {
                $menuClass = ' class="'. $options['menuClass'] .'"';
            } else $menuClass = '';

            return sprintf($menu, $menuClass, $items);
        }

        $this->includeFilters = true;
        $nestedList = $this->_getNestedList();

        return fillMenu($nestedList, $options);
    }

    public function getNestedIds(ProductCategory $model)
    {
        $result = $this->_getNestedChain($model);

        return array_column($result, 'ms_id');
    }

    public function getBreadCrumbs(ProductCategory $model)
    {
        $result = $this->_getParentChain($model);

        return array_reverse($result);
    }

    public function getUrlSegment(ProductCategory $model)
    {
        $segments = [];
        do {
            $segments[] = $model->slug;
        }
        while ($model = $model->parent);

        return implode('/', array_reverse($segments))
            .'/'. ProductCategory::LAST_NESTED_SLUG_MARKER;
    }

    public function getUrl(ProductCategory $model)
    {
        return Url::to(['/catalog/']) . $this->getUrlSegment($model);
    }
}
