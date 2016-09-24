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

            this.Map        = null;

            ymaps.ready(function()
            {
                self.Map = new ymaps.Map("map", {
                    center: [55.76, 37.64],
                    zoom: 13
                });

                console.log(self.get('geoobject'));
                console.log(self.get('outlets'));
                if (self.get('outlets').length > 0)
                {
                    _.each(self.get('outlets'), function(row, value)
                    {
                        console.log(value);
                        console.log(row);
                    });
                }

                /*myPlacemark = new ymaps.Placemark([55.76, 37.64], {
                    hintContent: 'Москва!',
                    balloonContent: 'Столица России'
                });

                self.Map.geoObjects.add(myPlacemark);*/
            });

        },

        _onDomReady: function()
        {
            var self = this;

            if ($("input[name=radioDelivery]").val())
            {
                this.switchDelivery($("input[name=radioDelivery]").val());
            }

            $("input[name=radioDelivery]").change(function(){
                self.switchDelivery($(this).val());
            });

            $('#search-address').fastLiveFilter('#search-address-list');

        },

        switchDelivery: function(value)
        {
            if (value === "SELF") {
                $('.delivery-form').not("#delivery-form-SELF").hide();
                $("#delivery-form-SELF").show();
            }
            if (value === "POST") {
                $('.delivery-form').not("#delivery-form-POST").hide();
                $("#delivery-form-POST").show();
            }
            if (value === "COURIER") {
                $('.delivery-form').not("#delivery-form-COURIER").hide();
                $("#delivery-form-COURIER").show();
            }
        }
    });
})(sx, sx.$, sx._);