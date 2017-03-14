/*!
 * @author Semenov Alexander <semenov@skeeks.com>
 * @link http://skeeks.com/
 * @copyright 2010 SkeekS (СкикС)
 * @date 23.09.2016
 */
(function(sx, $, _)
{
    sx.classes.V3toysDeliveryRadio = sx.classes.Component.extend({

        _init: function()
        {
            var self = this;
        },

        _onDomReady: function()
        {
            var self = this;

            this.Wrapper    = $("#" + this.get('wrapperId'));
            this.jElement   = $("#" + this.get('id'));

            //При смене выбора способа доставки, меняется значение в скрытом инпуте
            $("[type=radio]", this.Wrapper).change(function(){
                self.jElement.val($(this).val());
                self.jElement.change();
            });

            //После загрузки страницы, нужно отметить нужный radio выбранным
            var activeValue = this.jElement.val();
            if (!activeValue)
            {
                $("[type=radio]:first", this.Wrapper).click();
            }
            /*var activeValue = this.jElement.val();
            $("[type=radio]", this.Wrapper).val(activeValue);*/

        },
    });
})(sx, sx.$, sx._);