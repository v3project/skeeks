/*!
 * @author Semenov Alexander <semenov@skeeks.com>
 * @link http://skeeks.com/
 * @copyright 2010 SkeekS (СкикС)
 * @date 23.09.2016
 */
(function(sx, $, _)
{
    sx.classes.V3toysDelivery = sx.classes.Component.extend({

        _init: function()
        {
            var self = this;
        },

        _onDomReady: function()
        {
            var self = this;

            this.Wrapper = $("#" + this.get('id'));

            self
                ._initDeliveryTabs()
            ;

        },

        /**
         * @returns {sx.classes.V3toysDelivery}
         * @private
         */
        _initDeliveryTabs: function()
        {
            var self = this;

            if ($(".sx-deliveryChange", this.Wrapper).val())
            {
                this.switchDelivery($(".sx-deliveryChange", this.Wrapper).val());
            }

            $(".sx-deliveryChange", this.Wrapper).change(function(){
                self.switchDelivery($(this).val());
            });


            return this;
        },


        /**
         * @param value
         * @returns {sx.classes.V3toysDelivery}
         */
        switchDelivery: function(value)
        {
            $('.delivery-form', this.Wrapper).not(".delivery-form-" + value).hide();
            $(".delivery-form-" + value, this.Wrapper).show();

            return this;
        }
    });
})(sx, sx.$, sx._);