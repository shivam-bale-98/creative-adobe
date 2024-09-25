<?php
defined('C5_EXECUTE') or die("Access Denied.");

use Application\Concrete\Helpers\GeneralHelper;
use Application\Concrete\Helpers\SelectOptionsHelper;
use Application\Concrete\Models\Locale\App;
use Concrete\Core\Support\Facade\Express;
/** @var \Concrete\Core\Entity\Attribute\Key\PageKey $pk */
$pk               = $this->attributeKey;
$attribute_handle = $pk->getAttributeKeyHandle();

switch ($attribute_handle) {
    case 'related_products':
        /** @var \Concrete\Core\Utility\Service\Text $th */
        $th = Core::make('helper/text');
        $selectID = $this->field('value') . '_select';
        $textID = $this->field('value');
        $attrClassSelect = $this->getAttributeKey()->getAttributeKeyHandle() . '_select';
        $attrClassText = $this->getAttributeKey()->getAttributeKeyHandle() . '_text';

        $valueArray = [];
        $values = GeneralHelper::getProductOptions();
  
        if (is_object($this->attributeValue)) {
            $value = $th->entities($value);
            $valueArray = explode(',', $value);
        }
        echo $form->selectMultipleCheckbox( $selectID , $values ,$valueArray , array('class' =>  $attrClassSelect  ));
        print $form->hidden($textID, $value, array('class' => 'span4 ' . $attrClassText));

        ?>
        <script>
            $(".<?php echo $attrClassSelect; ?>").on('click', function () {
                var txtTextObj = $('.<?php echo $attrClassText; ?>');
                var selected   = [];

                $('.<?php echo $attrClassSelect; ?> :checked').each(function () {
                    selected.push($(this).val());
                });

                txtTextObj.val(selected.join(','));
            });
        </script>


    <?php
    break;
    case 'contact_locations' :

        $data = SelectOptionsHelper::getOptions('contact_locations');

        if (is_object($this->attributeValue)) {
            $th   = Loader::helper('text');
            $value      = $th->entities($this->attributeValue);
        }

        print $form->select( $this->field('value') , $data , $value );
     break;
    case 'related_applications':
        /** @var \Concrete\Core\Utility\Service\Text $th */
        $th = Core::make('helper/text');
        $selectID = $this->field('value') . '_select';
        $textID = $this->field('value');
        $attrClassSelect = $this->getAttributeKey()->getAttributeKeyHandle() . '_select';
        $attrClassText = $this->getAttributeKey()->getAttributeKeyHandle() . '_text';

        $valueArray = [];
        $values = GeneralHelper::getApplicationsOptions();

        if (is_object($this->attributeValue)) {
            $value = $th->entities($value);
            $valueArray = explode(',', $value);
        }
        echo $form->selectMultipleCheckbox( $selectID , $values ,$valueArray , array('class' =>  $attrClassSelect  ));
        print $form->hidden($textID, $value, array('class' => 'span4 ' . $attrClassText));

        ?>
        <script>
            $(".<?php echo $attrClassSelect; ?>").on('click', function () {
                var txtTextObj = $('.<?php echo $attrClassText; ?>');
                var selected   = [];

                $('.<?php echo $attrClassSelect; ?> :checked').each(function () {
                    selected.push($(this).val());
                });

                txtTextObj.val(selected.join(','));
            });
        </script>
        <?php
        break;

      default :
        print $form->text(
            $this->field('value'),
            $value,
            [
                'placeholder' => h($akTextPlaceholder)
            ]
        );
        break;
}