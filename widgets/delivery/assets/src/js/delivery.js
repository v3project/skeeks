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

            self.get('geoobject')

            this.MapObject = new sx.classes.ya.MapObject("map", {
                'ya' :
                { //Опции инициализации карты
                    'center' : [55.76, 37.64],
                    'zoom' : 10,
                    controls: ['zoomControl', 'fullscreenControl']
                }
            });;
        },

        _onDomReady: function()
        {
            var self = this;

            this.Wrapper = $("#" + this.get('id'));
            this.AddressList = $(".scroll-list", this.Wrapper);

            self
                ._initDeliveryTabs()
                ._initSearchAddress()
                ._initPoints()
            ;

        },

        /**
         * @private
         */
        _initPoints: function()
        {
            var self = this;


            this.MapObject.onReady(function(YaMap)
            {
                var LastPlacemark = null;

                $("li", self.AddressList).each(function()
                {
                    var jElement = $(this);
                    var Placemark = new ymaps.Placemark($(this).data('coords'), {
                        balloonContent: $(this).data('title')
                    }, {
                        preset: 'islands#violetStretchyIcon',
                    });

                    self.MapObject.YaMap.geoObjects.add(Placemark);

                    Placemark.events.add("balloonopen", function (event) {

                        var currentScroll = $("li:first", self.AddressList).offset().top;
                        var newPosition = jElement.offset().top - self.AddressList.offset().top + (self.AddressList.offset().top - currentScroll);

                        self.AddressList
                            .animate({
                                scrollTop: newPosition
                            }, 500, 'swing');

                        $("li", self.AddressList).removeClass("sx-active-outlet");
                        jElement.addClass("sx-active-outlet");


                        self.MapObject.YaMap.setCenter(Placemark.geometry.getCoordinates(), 13, {
                            duration: 800,
                            checkZoomRange: true,
                            callback: function()
                            {
                                Placemark.balloon.autoPan();
                            }
                        });
                    });

                    $(this).on('click', function()
                    {
                        /*$("li", self.AddressList).removeClass("sx-active-outlet");
                        $(this).addClass("sx-active-outlet");*/

                        Placemark.balloon.open();


                        return false;
                    });

                    LastPlacemark = Placemark;
                });

                if ($("li", self.AddressList).length > 1)
                {
                    self.MapObject.YaMap.setBounds(self.MapObject.YaMap.geoObjects.getBounds());
                } else
                {
                    self.MapObject.YaMap.setCenter(LastPlacemark.geometry.getCoordinates(), 13, {
                        checkZoomRange: true
                    });
                }
            });

            return this;
        },

        /**
         * @returns {sx.classes.V3toysDelivery}
         * @private
         */
        _initDeliveryTabs: function()
        {
            var self = this;

            if ($("input[name=radioDelivery]").val())
            {
                this.switchDelivery($("input[name=radioDelivery]").val());
            }

            $("input[name=radioDelivery]").change(function(){
                self.switchDelivery($(this).val());
            });


            return this;
        },

        /**
         * @returns {sx.classes.V3toysDelivery}
         * @private
         */
        _initSearchAddress: function()
        {
            $('#search-address').fastLiveFilter('#search-address-list');
            return this;
        },

        /**
         * @param value
         * @returns {sx.classes.V3toysDelivery}
         */
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

            return this;
        }
    });
})(sx, sx.$, sx._);