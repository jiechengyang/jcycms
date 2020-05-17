<?php
/**
 * @author: yjc
 * @email: 2064320087@qq.com
 * Created at: 2018-06-25 22:47
 */

namespace common\widgets;

use Yii;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\helpers\Url;

class ActiveField extends \yii\widgets\ActiveField
{

    public $options = ['class' => 'form-group'];

    // public $labelOptions = ['class' => 'col-sm-2 control-label];

    public $size = 10;

    // public $template = "{label}\n<div class=\"col-sm-{size}\">{input}\n{error}</div>\n{hint}";

    // public $errorOptions = ['class' => 'help-block m-b-none'];

    public function init()
    {
        parent::init();
        if (method_exists($this->model, 'autoCompleteOptions')) {
            $autoCompleteOptions = $this->model->autoCompleteOptions();
            if (isset($autoCompleteOptions[$this->attribute])) {
                if (is_callable($autoCompleteOptions[$this->attribute])) {
                    $this->autoComplete(call_user_func($autoCompleteOptions[$this->attribute]));
                } else {
                    $this->autoComplete($autoCompleteOptions[$this->attribute]);
                }
            }
        }
    }

    /**
     * autoComplete
     * options 插件参数:
     * @param data:数据源--同步请求直接传递数据源
     * @param url:异步请求数据来源的地址（*json）
     * @param isCache: 是否缓存 true使用prefetch[url为静态json文件] false使用remote
     * @param selectedByField: 选中后赋值的字段，该字段参与表单提交
     * @param selectedDomId:用于赋值
     * @param name: 数据集的名称
     * @param displayKey: 列表展示的字段
     * @param valueKey: 选中赋值到selectedByField的key
     * @param limit 显示条数
     * @author yjc
     */
    public function autoComplete($options)
    {
        static $counter = 0;
        $this->inputOptions['class'] .= ' typeahead typeahead-' . (++$counter);
        !isset($options['data']) && $options['data'] = null;
        !isset($options['url']) && $options['url'] = '';
        !isset($options['limit']) && $options['limit'] = 5;
        !isset($options['isCache']) && $options['isCache'] = false;
        if (isset($options['selectedByField']) && !empty($options['selectedByField'])) {
            $field = $this->form->field($this->model, $options['selectedByField']);
            $field->options['class'] .= ' hide';
            $field->inputOptions['id'] = $options['selectedDomId'];
            echo $field->hiddenInput()->label(false);
        }

        $this->form->getView()->registerJs("jcms.autocomplete($counter, " . Json::htmlEncode($options) . ");");
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function checkbox($options = [], $enclosedByLabel = false)
    {
        static $i = 1;
        $unique = uniqid() . $i;
        $i++;
        if ($i >= 10000) $i = 1;
        $for = 'inlineCheckbox' . $unique;
        $options['id'] = $for;
        $options['tag'] = 'a';
        $this->labelOptions = [];
        $this->options['class'] = '';
        $this->template = "<span class=\"checkbox checkbox-success checkbox-inline\">{input}
                              {label}
                            </span>";
        return parent::checkbox($options, $enclosedByLabel);
    }

    /**
     * @inheritdoc
     */
    public function checkboxList($items, $options = [])
    {

        $options['tag'] = 'ul';

        $inputId = Html::getInputId($this->model, $this->attribute);
        $this->selectors = ['input' => "#$inputId input"];

        $options['class'] = 'da-form-list inline';
        $encode = !isset($options['encode']) || $options['encode'];
        $itemOptions = isset($options['itemOptions']) ? $options['itemOptions'] : [];

        $unique = uniqid();
        $options['item'] = function ($index, $label, $name, $checked, $value) use ($encode, $itemOptions, $unique) {
            static $i = 1;
            $unique .= rand(1, 99999) . $i;
            $i++;
            if ($i >= 10000) $i = 1;
            $checkbox = Html::checkbox($name, $checked, array_merge($itemOptions, [
                'value' => $value,
                'id' => 'inlineCheckbox' . $unique,
            ]));

            return "<li class='checkbox checkbox-success checkbox-inline'>
                        $checkbox
                        <label for='inlineCheckbox{$unique}'> {$label} </label>
                    </li>";
        };
        return parent::checkboxList($items, $options);
    }

    /**
     * @param array $options
     * @return \yii\widgets\ActiveField
     */
    public function fileInput($options = [])
    {
        if (!isset($options['class'])) {
            $options['class'] = 'pretty-file';
        } else {
            $options['class'] .= ' pretty-file';
        }
        !isset($options['text']) && $options['text'] = Yii::t("app", 'Choose File');
        return parent::fileInput($options); // TODO: Change the autogenerated stub
    }

    /**
     * @param array $options
     * @return $this
     */
    public function imgInput($options = [])
    {
        if ($this->template === "{label}\n<div class=\"col-sm-{size}\">{input}\n{error}</div>\n{hint}") {
            $this->template = "{label}\n<div class=\"col-sm-{size} image\">{input}<div style='position: relative'>{img}{actions}</div>\n{error}</div>\n{hint}";
        }
        $attribute = $this->attribute;
        $src = key_exists('value', $options) ? $options['value'] : $this->model->$attribute;
        $baseUrl = Yii::$app->request->baseUrl;
        $nonePicUrl = isset($options['default']) ? $options['default'] : $baseUrl . 'static/images/none.jpg';
        if ($src != '') {
            if (strpos($src, $baseUrl) !== 0) {
                $temp = parse_url($src);
//                $src = (! isset($temp['host'])) ? $cdn->getCdnUrl($src) : $src;
            }
            $delete = Yii::t('app', 'Delete');
            $this->parts['{actions}'] = "<div onclick=\"$(this).parents('.image').find('input[type=hidden]').val(0);$(this).prev().attr('src', '$nonePicUrl');$(this).remove()\" style='position: absolute;width: 50px;padding: 5px 3px 3px 5px;top:5px;left:6px;background: black;opacity: 0.6;color: white;cursor: pointer'><i class='fa fa-trash' aria-hidden='true'> {$delete}</i></div>";
        } else {
            $src = $nonePicUrl;
            $this->parts['{actions}'] = '';
        }
        if (!isset($options['class'])) {
            $options['class'] = 'pretty-file img-responsive';
        } else {
            $options['class'] .= ' pretty-file img-responsive';
        }
        !isset($options['text']) && $options['text'] = Yii::t("app", 'Choose Image');
        $this->parts['{img}'] = Html::img($src, array_merge($options, ["nonePicUrl" => $nonePicUrl]));
        return parent::fileInput($options); // TODO: Change the autogenerated stub
    }


    /**
     * 时间/日期输入框
     *
     * @param array $options
     * - val: string 值，替代html的value属性，设置此val会在页面加载完成后由js把value改为val，此处与laydate不同之处，需要注意
     * - type: string，输入框类型，默认date。可选值：
     * year    年选择器    只提供年列表选择
     * month    年月选择器    只提供年、月选择
     * date    日期选择器    可选择：年、月、日。type默认值，一般可不填
     * time    时间选择器    只提供时、分、秒选择
     * datetime    日期时间选择器    可选择：年、月、日、时、分、秒
     * - range: bool/string， 开启左右面板范围选择，默认false。如果设置 true，将默认采用 “ ~ ” 分割。 你也可以直接设置 分割字符。五种选择器类型均支持左右面板的范围选择。
     * - theme: string，主题，默认值：default。可选值有：default（默认简约）、molv（墨绿背景）、#颜色值（自定义颜色背景）、grid（格子主题）
     * ...更多的设置请直接参考laydate官方文档: https://www.layui.com/doc/modules/laydate.html
     * @return $this
     */
    public function date($options = [])
    {
        !isset($options['elem']) && $options['elem'] = 'this';
        !isset($options['type']) && $options['type'] = 'datetime';
        !isset($options['range']) && $options['range'] = false;
        $options['range'] === true && $options['range'] = '~';
        $options['range'] === false && $options['range'] = 'false';
        !isset($options['format']) && $options['format'] = 'yyyy-MM-dd HH:mm:ss';
        !isset($options['val']) && $options['val'] = $this->model->{$this->attribute} ? $this->model->{$this->attribute} : (strpos(get_class($this->model), 'Search') !== false ? '' : 'new Date()');
        !isset($options['isInitValue']) && $options['isInitValue'] = false;
        $options['isInitValue'] === true && $options['isInitValue'] = 'true';
        $options['isInitValue'] === false && $options['isInitValue'] = 'false';
        !isset($options['min']) && $options['min'] = '1900-1-1';
        !isset($options['max']) && $options['max'] = '2099-12-31';
        !isset($options['trigger']) && $options['trigger'] = 'focus';
        !isset($options['show']) && $options['show'] = false;
        $options['show'] === true && $options['show'] = 'true';
        $options['show'] === false && $options['show'] = 'false';
        !isset($options['position']) && $options['position'] = 'absolute';
        !isset($options['zIndex']) && $options['zIndex'] = '66666666';
        !isset($options['showBottom']) && $options['showBottom'] = true;
        $options['showBottom'] === true && $options['showBottom'] = 'true';
        $options['showBottom'] === false && $options['showBottom'] = 'false';
        !isset($options['btns']) && $options['btns'] = "['clear', 'now', 'confirm']";
        !isset($options['lang']) && $options['lang'] = (strpos(Yii::$app->language, 'en') === 0 ? 'en' : 'cn');
        !isset($options['theme']) && $options['theme'] = 'molv';
        !isset($options['calendar']) && $options['calendar'] = true;
        $options['calendar'] === true && $options['calendar'] = "true";
        $options['calendar'] === false && $options['calendar'] = "false";
        !isset($options['mark']) && $options['mark'] = '{}';//json对象
        !isset($options['ready']) && $options['ready'] = 'function(date){}';//匿名函数
        !isset($options['change']) && $options['change'] = 'function(value, date, endDate){}';//匿名函数
        !isset($options['done']) && $options['done'] = 'function(value, date, endDate){}';//匿名函数
        $options['dateType'] = $options['type'];
        $options['search'] = 'true';
        unset($options['type']);

        if (!isset($options['class'])) {
            $options['class'] = 'form-control date-time';
        } else {
            $options['class'] .= ' form-control date-time';
        }
        $this->parts['{input}'] = Html::activeTextInput($this->model, $this->attribute, $options);
        return $this;
    }

    /**
     * 美化过的select选框
     *
     * @param array $items 需要设置的option元素,数组key作为值，数组value显示为option选项内容
     * @param bool $multiple 是否多选，默认单选
     * @param array $options htmp属性设置
     *  - 具体的参数配置请参考jquery chosen官方文档: https://harvesthq.github.io/chosen/options.html
     * @param bool $generateDefault 是否生成请选择选项，默认是
     * @return \yii\widgets\ActiveField
     */
    public function chosenSelect($items, $multiple = false, $options = [], $generateDefault = true)
    {
        if (isset($options['class'])) {
            $options['class'] .= " chosen-select";
        } else {
            $options['class'] = "chosen-select";
        }
        $multiple && $options['multiple'] = "1";
        !isset($options['allow_single_deselect']) && $options['allow_single_deselect'] = true;
        $options['allow_single_deselect'] === true && $options['allow_single_deselect'] = 'true';
        $options['allow_single_deselect'] === false && $options['allow_single_deselect'] = 'false';
        !isset($options['disable_search']) && $options['disable_search'] = false;
        $options['disable_search'] === true && $options['disable_search'] = 'true';
        $options['disable_search'] === false && $options['disable_search'] = 'false';
        !isset($options['disable_search_threshold']) && $options['disable_search_threshold'] = 0;
        !isset($options['enable_split_word_search']) && $options['enable_split_word_search'] = true;
        $options['enable_split_word_search'] === true && $options['enable_split_word_search'] = 'true';
        $options['enable_split_word_search'] === false && $options['enable_split_word_search'] = 'false';
        !isset($options['inherit_select_classes']) && $options['inherit_select_classes'] = false;
        $options['inherit_select_classes'] === true && $options['inherit_select_classes'] = 'true';
        $options['inherit_select_classes'] === false && $options['inherit_select_classes'] = 'false';
        !isset($options['max_selected_options']) && $options['max_selected_options'] = 'Infinity';
        !isset($options['no_results_text']) && $options['no_results_text'] = Yii::t('app', 'None');
        !isset($options['placeholder_text_multiple']) && $options['placeholder_text_multiple'] = Yii::t('app', 'Please select some');;
        !isset($options['placeholder_text_single']) && $options['placeholder_text_single'] = Yii::t('app', 'Please select');
        !isset($options['search_contains']) && $options['search_contains'] = true;
        $options['search_contains'] === true && $options['search_contains'] = 'true';
        $options['search_contains'] === false && $options['search_contains'] = 'false';
        !isset($options['group_search']) && $options['group_search'] = true;
        $options['group_search'] === true && $options['group_search'] = 'true';
        $options['group_search'] === false && $options['group_search'] = 'false';
        !isset($options['single_backstroke_delete']) && $options['single_backstroke_delete'] = true;
        $options['single_backstroke_delete'] === true && $options['single_backstroke_delete'] = 'true';
        $options['single_backstroke_delete'] === false && $options['single_backstroke_delete'] = 'false';
        !isset($options['width']) && $options['width'] = '100%';
        !isset($options['display_disabled_options']) && $options['display_disabled_options'] = true;
        $options['display_disabled_options'] === true && $options['display_disabled_options'] = 'true';
        $options['display_disabled_options'] === false && $options['display_disabled_options'] = 'false';
        !isset($options['display_selected_options']) && $options['display_selected_options'] = true;
        $options['display_selected_options'] === true && $options['display_selected_options'] = 'true';
        $options['display_selected_options'] === false && $options['display_selected_options'] = 'false';
        !isset($options['include_group_label_in_selected']) && $options['include_group_label_in_selected'] = false;
        $options['include_group_label_in_selected'] === true && $options['include_group_label_in_selected'] = 'true';
        $options['include_group_label_in_selected'] === false && $options['include_group_label_in_selected'] = 'false';
        !isset($options['max_shown_results']) && $options['max_shown_results'] = 'Infinity';
        !isset($options['case_sensitive_search']) && $options['case_sensitive_search'] = false;
        $options['case_sensitive_search'] === true && $options['case_sensitive_search'] = 'true';
        $options['case_sensitive_search'] === false && $options['case_sensitive_search'] = 'false';
        !isset($options['hide_results_on_select']) && $options['hide_results_on_select'] = true;
        $options['hide_results_on_select'] === true && $options['hide_results_on_select'] = 'true';
        $options['hide_results_on_select'] === false && $options['hide_results_on_select'] = 'false';
        !isset($options['rtl']) && $options['trl'] = false;
        $options['trl'] === true && $options['trl'] = 'true';
        $options['trl'] === false && $options['trl'] = 'false';
        return $this->dropDownList($items, $options, $generateDefault);
    }

    public function dropDownList($items, $options = [], $generateDefault = true)
    {
        if ($generateDefault === true && !isset($options['prompt'])) {
            $options['prompt'] = Yii::t('app', 'Please select');
        }
        return parent::dropDownList($items, $options);
    }

    /**
     * @inheritdoc
     */
    public function readOnly($value = null, $options = [])
    {
        $options = array_merge($this->inputOptions, $options);

        $this->adjustLabelFor($options);
        $value = $value === null ? Html::getAttributeValue($this->model, $this->attribute) : $value;
        $options['class'] = 'da-style';
        $options['style'] = 'display: inline-block;';
        $this->parts['{input}'] = Html::activeHiddenInput($this->model, $this->attribute) . Html::tag('span', $value, $options);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function radioList($items, $options = [])
    {
        $options['tag'] = 'div';

        $inputId = Html::getInputId($this->model, $this->attribute);
        $this->selectors = ['input' => "#$inputId input"];

        $options['class'] = 'radio';
        $encode = !isset($options['encode']) || $options['encode'];
        $itemOptions = isset($options['itemOptions']) ? $options['itemOptions'] : [];

        $options['item'] = function ($index, $label, $name, $checked, $value) use ($encode, $itemOptions) {
            static $i = 1;
            $radio = Html::radio($name, $checked, array_merge($itemOptions, [
                'value' => $value,
                'id' => $name . $i,
                //'label' => $encode ? Html::encode($label) : $label,
            ]));
            $radio .= "<label for=\"$name$i\"> $label </label>";
            $radio = "<div class='radio radio-success radio-inline'>{$radio}</div>";
            //var_dump($radio);die;
            $i++;
            return $radio;
        };
        return parent::radioList($items, $options);
    }

    /**
     * @inheritdoc
     */
    public function textarea($options = [])
    {
        if (!isset($options['rows'])) {
            $options['rows'] = 5;
        }
        return parent::textarea($options);
    }

    public function catalogDropDownList($data = [], $options = [])
    {
        $inputId = Html::getInputId($this->model, $this->attribute);
        $inputName = Html::getInputName($this->model, $this->attribute);
        $url = Url::to(['mall-spu/ajax-catalog']);
        $tips = '请选择商品类别';
        return <<<HTML
            <div class="form-group">
                    <div onclick="selectDiv.selectClass()" class="selectDiv"
                    data-url="{$url}">
                        <select name="{$inputName}" readonly="readonly"
                        class="form-control"
                        id="{$inputId}"
                        aria-required="true"
                        style="margin-right: 0px;">
                            <option selected="selected" value="0">{$tips}</option>
                        </select>
                        <div id="div_text"></div>
                    </div>
                    <div id="selectData" class="formInputDiv" style="display: none;">
                    <ul id="selectData_1"></ul>
                </div>
            </div>
HTML;
    }

}