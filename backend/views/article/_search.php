<?php

use yii\helpers\Html;
use common\models\Category;
use yii\widgets\ActiveForm AS BAF;
use common\components\Utils;
use common\components\BaseConfig;

/* @var $this yii\web\View */
/* @var $model backend\models\search\ArticleSearch */
/* @var $form yii\widgets\ActiveForm */
$categoryTree = Utils::reference_delivery_tree(Category::find()->asArray()->all(), 'id', 'parent_id');

?>

<div class="article-search">
    <div class="row">
        <?php $form = BAF::begin([
            'action' => ['index'],
            'method' => 'post',
            'options' => ['class' => 'form-inline'],
        ]); ?>
        <div class="col-sm-12">
            <?= $form->field($model, 'category_id')->dropDownList(Category::getDrowDownList($categoryTree), ['prompt' => '请选择', 'style' => 'width:200px']) ?>

            <?= $form->field($model, 'title') ?>

            <?= $form->field($model, 'flag_headline')->dropDownList(BaseConfig::getYesNoItems(), ['prompt' => '请选择']) ?>

            <?= $form->field($model, 'flag_recommend')->dropDownList(BaseConfig::getYesNoItems(), ['prompt' => '请选择']) ?>

            <?= $form->field($model, 'flag_slide_show')->dropDownList(BaseConfig::getYesNoItems(), ['prompt' => '请选择']) ?>
        </div>
        <div class="col-sm-12">
            <?= $form->field($model, 'flag_special_recommend')->dropDownList(BaseConfig::getYesNoItems(), ['prompt' => '请选择']) ?>

            <?= $form->field($model, 'flag_roll')->dropDownList(BaseConfig::getYesNoItems(), ['prompt' => '请选择']) ?>

            <?= $form->field($model, 'flag_bold')->dropDownList(BaseConfig::getYesNoItems(), ['prompt' => '请选择']) ?>

            <?= $form->field($model, 'flag_picture')->dropDownList(BaseConfig::getYesNoItems(), ['prompt' => '请选择']) ?>

            <?= $form->field($model, 'status')->dropDownList(BaseConfig::getArticleStatus(), ['prompt' => '请选择']) ?>
            <div class="form-group"  style="padding-bottom:10px;">
                <?= Html::submitButton(Yii::t('app', 'Search'), ['class' => 'btn btn-primary']) ?>
                <?= Html::resetButton('<i class="fa fa-undo"></i>' . Yii::t('app', 'Clear Query'), ['class' => 'btn btn-default clear-search']) ?>
            </div>
        </div>
    </div>
    <?php BAF::end(); ?>

</div>
