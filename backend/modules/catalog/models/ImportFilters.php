<?php

namespace backend\modules\catalog\models;

use common\models\Product;
use common\models\ProductCategory;
use common\models\ProductFilters;
use common\models\ProductFiltersCategory;
use Yii;
use yii\base\Model;
use yii\validators\InlineValidator;
use yii\web\UploadedFile;
use moonland\phpexcel\Excel;

class ImportFilters extends Model
{
    public $success_count = 0;
    public $deleted_all = false;
    public $xlsx = null;

    private $source;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['xlsx', 'required'],
            ['xlsx', 'file', 'skipOnEmpty' => false],
           // ['xlsx', 'file', 'skipOnEmpty' => false, 'extensions' => 'xlsx, xls', 'message' => 'Файл должен быть в формате .xls(x)'],
            [
                'xlsx',
                InlineValidator::className(),
                'method' => [$this, 'validateXlsx'],
                'params' => [
                    'attributes' => [
                        'Артикул',
                        'Фильтр',
                        'Значение'
                    ]
                ],
            ],
            ['deleted_all', 'integer']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'xlsx' => t_b( 'Файл excel'),
            'deleted_all' => t_b( 'Удалить все фильтры перед импортом'),
        ];
    }

    public function load($data, $formName = null)
    {
        if (isset($data)) {
            $result = parent::load($data, $formName);

            if (Yii::$app->request->isPost) {
                $uploadedFile = UploadedFile::getInstance($this, 'xlsx');
                if ($uploadedFile)
                    $this->xlsx = $uploadedFile;

                $this->source = Excel::import($this->xlsx->tempName, [
                    'setFirstRecordAsKeys' => true,
                    'setIndexSheetByName' => false,
                    //'getOnlySheet' => 'sheet1',
                ]);

                return $result;
            }
        }

        return false;
    }

    public function validateXlsx($attribute, $params)
    {
        if ($this->xlsx instanceof UploadedFile) {
            $requiredFields = isset($params['attributes']) ? $params['attributes'] : [];
            if (!count($requiredFields)) {
                return;
            }

            $result = true;

            if (isset($this->source[0])) {
                foreach($requiredFields as $field)
                    if (!in_array($field, array_keys($this->source[0]))) {
                        $result = false;
                        break;
                    }
            } else $result = false;

            if (!$result) {
                $this->addError('Xlsx', "Недостаточно данных для импорта. Обязательно должны присутствовать поля: " .
                    implode(", ", $requiredFields)
                );
            }
        }
    }

    public function exportData() {
        $result = [];
        foreach(Product::find()->all() as $p) {

            foreach($p->productFilters as $f) {
                if (in_array($f->category_id, explode(',', $p->category->filterCatIds))) {
                    $result[] = new ExportFilterModel([
                        'article' => $p->article,
                        'filter' => $f->category->title,
                        'value' => $f->title
                    ]);
                }
            }
        }

        return $result;
    }

    public function import() {

        if ($this->deleted_all) {
            ProductFilters::deleteAll();
            ProductFiltersCategory::deleteAll();
        }

        foreach($this->source as $row) {
            $art = trim($row['Артикул']);
            $filter = trim($row['Фильтр']);
            $val = trim($row['Значение']);

            if ($p = Product::findOne(['article' => $art])) {
                if($c = ProductCategory::findOne($p->category->id)) {
                    $status = true;

                    $f = ProductFiltersCategory::findOne(['title' => $filter]);
                    if (!$f && $status) {
                        $f = new ProductFiltersCategory(['title' => $filter, 'status' => 1]);
                        if (!$f->save()) $status = false;
                    }
                    $v = ProductFilters::findOne(['title' => $val]);
                    if (!$v && $status) {
                        $v = new ProductFilters(['title' => $val, 'category_id' => $f->id, 'status' => 1]);
                        if (!$v->save()) $status = false;
                    }

                    if ( $status ) {
                        $c->productFiltersCategories = array_merge(explode(',', $c->filterCatIds), [$f->id]);
                        if (!$c->save()) $status = false;
                    }

                    if ( $status ) {
                        $p->productFilters = array_merge(explode(',', $p->productFiltersIds), [$v->id]);
                        if (!$p->save()) $status = false;
                    }

                    if ($status)
                        $this->success_count = $this->success_count+1;
                }
            }

        }

        return true;
    }

}
