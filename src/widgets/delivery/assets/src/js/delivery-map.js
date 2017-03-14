/*!
 * @author Semenov Alexander <semenov@skeeks.com>
 * @link http://skeeks.com/
 * @copyright 2010 SkeekS (СкикС)
 * @date 23.09.2016
 */
(function(sx, $, _)
{
    sx.classes.V3toysDeliveryMap = sx.classes.Component.extend({

        _init: function()
        {
            var self = this;

            var coord = [55.76, 37.64];
            if (self.get('coordinates'))
            {
                coord = self.get('coordinates');
            }

            this.MapObject = new sx.classes.ya.MapObject(this.get('mapId'), {
                'ya' :
                { //Опции инициализации карты
                    'center' : coord,
                    'zoom' : 11,
                    'autoFitToViewport' : 'alvays',
                    controls: ['zoomControl', 'fullscreenControl']
                }
            });

            this.bind('change', function(e, data)
            {
                self.jElement.val(data.jElement.data('v3p_outlet_id'));
                self.jElement.change();

                var jActive = self.getActiveElement();
                if (jActive.length > 0)
                {
                    $(".sx-map-point-selected", self.jWrapper).show();
                    $(".sx-map-point-selected span", self.jWrapper).empty().append(jActive.data('title'));
                }
            });
        },

        getActiveElement: function()
        {
            return $(".sx-active-outlet");
        },

        _onDomReady: function()
        {
            var self = this;

            this.jElement = $("#" + this.get('id'));

            this.jWrapper = $("#" + this.get('wrapperId'));
            this.AddressList = $(".scroll-list", this.jWrapper);

            self
                ._initSearchAddress()
                ._initPoints()
            ;

        },

        /**
         * @returns {sx.classes.V3toysDeliveryMap}
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

                        self.trigger('change', {'jElement': jElement, 'Placemark': Placemark});
                    });

                    $(this).on('click', function()
                    {
                        Placemark.balloon.open();
                        return false;
                    });

                    LastPlacemark = Placemark;
                });

                _.delay(function()
                {
                    if ($("li", self.AddressList).length > 1)
                    {
                        //self.MapObject.YaMap.setBounds(self.MapObject.YaMap.geoObjects.getBounds());
                    } else
                    {
                        if (LastPlacemark)
                        {
                            /*self.MapObject.YaMap.setCenter(LastPlacemark.geometry.getCoordinates(), 13, {
                                checkZoomRange: true
                            });*/
                        }
                    }
                }, 300);

                _.delay(function()
                {
                    if ($("#sx-outlet-" + self.jElement.val()).length > 0)
                    {
                        $("#sx-outlet-" + self.jElement.val()).click();
                    } else
                    {
                        self.jElement.val('');
                    }

                }, 400);
            });

            return this;
        },

        /**
         * @returns {sx.classes.V3toysDeliveryMap}
         * @private
         */
        _initSearchAddress: function()
        {
            $('#search-address').fastLiveFilter('#search-address-list');
            return this;
        },
    });
})(sx, sx.$, sx._);