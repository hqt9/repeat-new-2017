(function(root) {
  var _ = root._,
      $ = root.jQuery;

  window.mobilecheck = function() {
    var check = false;
    (function(a,b){if(/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od|ad)|iris|kindle|lge |maemo|midp|mmp|mobile.+firefox|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows ce|xda|xiino/i.test(a)||/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i.test(a.substr(0,4)))check = true})(navigator.userAgent||navigator.vendor||window.opera);
    var _w = $(window).width();
    return check && (_w > 960);
  };

  window.is_int = function(n) {
    if (_.isArray(n) === true) {
      return false;
    }
    
    return n % 1 === 0;
  };

  $.fn.serializeObject = function() {
    var o = {};
    var a = this.serializeArray();
    $.each(a, function() {
      if (o[this.name] !== undefined) {
        if (!o[this.name].push) {
            o[this.name] = [o[this.name]];
        }
        o[this.name].push(this.value || '');
      } else {
        o[this.name] = this.value || '';
      }
    });
    return o;
  };

  Math.radians = function(degrees) {
    return degrees * Math.PI / 180;
  };

  Math.greatCircleDistance = function(lat1, lon1, lat2, lon2) {
    var R = 6371000,
        th1 = Math.radians(lat1),
        th2 = Math.radians(lat2),
        delta1 = Math.radians(lat2 - lat1),
        delta2 = Math.radians(lon2 - lon1);

    var a = Math.sin(delta1 / 2) * Math.sin(delta1 / 2) +
            Math.cos(th1) * Math.cos(th2) * Math.sin(delta2 / 2) * Math.sin(delta2 / 2);

    var c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));

    return R * c;
  };

  var ecif;
  ecif = root.ecif = {};

  ecif.Model = {};
  ecif.Collection = {};
  ecif.View = {};

  ecif.Ajax = function(url, data, options) {
    options || (options = {});

    var callback, faliure;

    if (_.isUndefined(options['callback'])) {
      callback = function() {};
    } else {
      callback = options['callback'];
    }

    if (_.isUndefined(options['failure'])) {
      failure = function() {};
    } else {
      failure = options['failure'];
    }

    _.extend(data, { form_key: formKey });
    $.ajax({
      url: url,
      data: data,
      type: 'POST',
      beforeSend: function() { $.fancybox.showLoading(); }
    }).done(function(response) {
      $.fancybox.hideLoading();
      response = $.parseJSON(response);
      if (response.success === true) {
        callback(response);
      } else {
        failure(response);
      }
    }).fail(function(response) {
      $.fancybox.hideLoading();
      response = $.parseJSON(response);
      failure(response);
    });
  };

  // Model
  ecif.Model.Base = Backbone.Model.extend({
    defaults: {
      'timestamp': -1
    }
  });

  ecif.Model.Store = ecif.Model.Base.extend({
    constructor: function() {
      Backbone.Model.apply(this, arguments);
    }
  });

  ecif.Model.Country = ecif.Model.Base.extend({
    constructor: function() {
      Backbone.Model.apply(this, arguments);
    }
  });

  // Collection
  ecif.Collection.StoreCollection = Backbone.Collection.extend({
    model: ecif.Model.Store,
    localStorage: new Backbone.LocalStorage('ecif.storelocator,stores'),
    update: function(_stores) {
      var _self = this;
      _.each(_stores, function(_store, idx) {
        var store = _self.get(idx);
        _store['id'] = _store['entity_id'];
        if (_.isUndefined(store)) {
          _self.create(_store);
        } else {
          store.set(_store).save();
        }
      });
    }
  });

  ecif.Collection.CountryCollection = Backbone.Collection.extend({
    model: ecif.Model.Country,
    localStorage: new Backbone.LocalStorage('ecif.storelocator,countries'),
    update: function(_countries) {
      var _self = this;
      _.each(_countries, function(_country, idx) {
        var country = _self.get(idx),
            data = {
              id: idx,
              code: idx,
              name: _country
            };
        if (_.isUndefined(country)) {
          _self.create(data);
        } else {
          country.set(data).save();
        }
      });
    }
  });

  // View
  ecif.View.StoreFilterView = Backbone.View.extend({
    events: {
      'change #select-continent': 'changeContinent',
      'change #select-country': 'changeCountry',
      'change #select-city': 'changeCity',
      'click #check-flagship': 'changeFilterFlagship',
      'click #check-repeat': 'changeFilterRepeat',
      'click #check-retailers': 'changeFilterRetailers',
      'click .show-results' : 'showResults',
    },
    initialize: function(options) {
      var _self = this;

      options || (options = {});

      this.template = _.template(options.template.html());
      this._event = options._event;

      this.collection = options.collection;
      this.countries = options.countries;
      this.defaultCountry = options.defaultCountry;
      this.store = null;
      this.constrains = {
        flagship: true,
        repeat: true,
        retailers: false
      };

      _self.filters = {};
      this.collection.each(function(store) {
        var continent = store.get('continent'),
            country = store.get('country'),
            city = store.get('city');
        if (_.isUndefined(_self.filters[continent])) {
          _self.filters[continent] = {};
        }
        if (_.isUndefined(_self.filters[continent][country])) {
          _self.filters[continent][country] = [];
        }
        if (_.indexOf(_self.filters[continent][country], city) === -1) {
          _self.filters[continent][country].push(city);
        }
      });

      this.currentCountry = this.defaultCountry;

      for (var key in this.filters) { 
        if (_.isUndefined(this.filters[key][this.currentCountry]) == false) {
          break;
        }
      }
      this.currentContinent = key;

      switch (storeView) {
      case 'en':
      case 'nl':
        this.currentCity = 'Amsterdam';
        break;
      case 'de':
        this.currentCity = 'Frankfurt';
        break;
      case 'fr':
        this.currentCity = 'Paris';
        break;
      case 'ch_de':
        this.currentCity = 'Bern';
        break;
      case 'ch_fr':
        this.currentCity = 'Genève';
        break;
      default:
        this.currentCity = this.filters[this.currentContinent][this.currentCountry][0];
      }
    },
    changeFilterFlagship: function(event) {
      this.constrains.flagship = $('#check-flagship').is(':checked');
      this.render();
    },
    changeFilterRepeat: function(event) {
      this.constrains.repeat = $('#check-repeat').is(':checked');
      this.render();
    },
    changeFilterRetailers: function(event) {
      this.constrains.retailers = $('#check-retailers').is(':checked');
      this.render();
    },
    _changeContinent: function(continent) {
      this.currentContinent = continent;
      for (var key in this.filters[this.currentContinent]) { break; }
      this.currentCountry = key;
      this.currentCity = this.filters[this.currentContinent][this.currentCountry][0];
      this.store = null;
      this.render();
    },
    changeContinent: function(event) {
      this._changeContinent($(event.target).val());
    },
    _changeCountry: function(country) {
      this.currentCountry = country;
      this.currentCity = this.filters[this.currentContinent][this.currentCountry][0];
      if (country == 'FR') {
        this.currentCity = 'Paris';
      }
      this.store = null;
      this.render();
    },
    changeCountry: function(event) {
      this._changeCountry($(event.target).val());
    },
    _changeCity: function(city) {
      this.currentCity = city;
      this.store = null;
      this.render();
    },
    changeCity: function(event) {
      this._changeCity($(event.target).val());
    },

    showResults: function(){
      $('body').addClass('open-page');
      $('.store-locator .store-list').scrollTop(0);
      return false
    },

    getStores: function() {
      var stores = this.collection.where({
        continent: this.currentContinent,
        country: this.currentCountry,
        city: this.currentCity
      }).toArray();

      var self = this;
      if ((self.constrains.flagship && self.constrains.repeat && self.constrains.retailers) || 
          (! self.constrains.flagship && ! self.constrains.repeat && ! self.constrains.retailers)) {
        // do nothing
      } else {
        stores = _.filter(stores, function(store) {
          if (self.constrains.flagship === false && store.get('is_flag_store') == '1') {
            return false;
          }

          if (self.constrains.repeat === false && store.get('is_repeat_store') == '1') {
            return false;
          }

          if (self.constrains.retailers === false && store.get('is_retail_store') == '1') {
            return false;
          }

          return true;
        });
      }

      // restore if the filtered set is empty
      if (stores.length == 0) {
        stores = this.collection.where({
          continent: this.currentContinent,
          country: this.currentCountry,
          city: this.currentCity
        }).toArray();

        _.each(stores, function(store) {
          if (store.get('is_flag_store') == '1') {
            self.constrains.flagship = true;
            return true;
          }

          if (store.get('is_repeat_store') == '1') {
            self.constrains.repeat = true;
            return true;
          }

          if (store.get('is_retail_store') == '1') {
            self.constrains.retailers = true;
            return true;
          }
        });

        $('.block-filter .entry.checkbox input').prop('checked', true);
      }

      stores = _.sortBy(stores, function(store) { return store.get('is_flag_store') });
      stores.reverse();

      return stores;
    },
    nearest: function(_latitude, _longitude) {
      var stores = this.collection.toArray();

      stores = _.sortBy(stores, function(store) { 
        return Math.greatCircleDistance(
          parseFloat(store.get('latitude')), parseFloat(store.get('longitude')),
          _latitude, _longitude
        );
      });

      return stores.slice(0, 10);
    },
    get: function(key) {
      return this[key];
    },
    set: function(key, value) {
      this[key] = value;
      return this;
    },
    render: function() {
      var data = {
        constrains: this.constrains
      };
      if (this.store !== null) {
        this.currentContinent = this.store.get('continent');
        this.currentCountry = this.store.get('country');
        this.currentCity = this.store.get('city');
      }

      data.currentContinent = this.currentContinent;
      data.currentCountry = this.currentCountry;
      data.currentCity = this.currentCity;

      data.continents = [];
      data.countries = [];
      data.cities =[];
      for (var continent in this.filters) {
        data.continents.push(continent);
        if (continent === data.currentContinent) {
          for (var country in this.filters[continent]) {
            var c = this.countries.where({id: country});
            data.countries.push(c[0]);
            if (country === data.currentCountry) {
              data.cities =
                _.sortBy(
                  this.filters[continent][country],
                  function(city) { return city; }
                );
            }
          }
        }
      }

      data.countries = _.sortBy(data.countries, function(country) { return country.get('name'); });

      this.$el.html(this.template(data));
      this.$el.find('select').customSelect();
      this._event.trigger('filter:change');

      return this;
    }
  });

  ecif.View.StoreListView = Backbone.View.extend({
    events: {
      'click .store-info>li': 'changeStore',
      'click .btn-store-info': 'storeDetail',
      'click .btn-view-on-map': 'clickViewOnMap',
      'click .btn-mobile-detail': 'mobileDetail',
      // scroll up
      'click .btn-scroll-up': 'scrollUpClick',
      'mouseover .btn-scroll-up': 'scrollUpStart',
      'mouseout .btn-scroll-up': 'scrollUpEnd',
      // scroll down
      'click .btn-scroll-down': 'scrollDownClick',
      'mouseover .btn-scroll-down': 'scrollDownStart',
      'mouseout .btn-scroll-down': 'scrollDownEnd',
      'mousewheel .store-list .content': 'scroll',

      'click .page-back' : 'hideResults'
    },
    initialize: function(options) {
      options || (options = {});
      this.collect = options.collect;
      this.collection = options.collection;
      this.template = _.template(options.template.html());
      this.router = options.router;
      this._event = options._event;
      this._step = 30;
    },
    _changeStore: function(storeId) {
      this.currentStore = storeId.toString();
      var store = this.getStore();
      this.router.navigate(
        '/' + store.get('url_key') + '/',
        { trigger: true }
      );
    },
    changeStore: function(event) {
      this._changeStore.call(
        this,
        $(event.target).closest('.store-info > li').data('id')
      );
      return this;
    },
    storeDetail: function(event) {
      this._changeStore($(event.target).closest('li').data('id'));
      this._event.trigger('store:detail', this.getStore());
      return false;
    },
    clickViewOnMap: function() {
      if (mobilecheck() === true) {
        window.open($(this).attr('href'));
        return false;
      }
    },
    mobileDetail: function(event) {
      var $detail = $(event.target).closest('li').find('.mobile-detail');
      if ($detail.hasClass('hidden') === true) {
        $detail.removeClass('hidden');
      } else {
        $detail.addClass('hidden');
      }
      return false;
    },
    getStore: function() {
      var stores = this.collection.where({ entity_id: this.currentStore });
      return stores[0];
    },
    hideResults: function(){
      $('body').removeClass('open-page');
      return false;
    },
    _scrollTo: function(top) {
      var $el = this.$el,
          $container = $el.find('.store-info'),
          parentHeight = $container.parent().height(),
          height = $container.height(),
          diff = height - parentHeight;

      if (top > 0) { top = 0; }
      if (Math.abs(top) > diff) {
        top = '-' + diff + 'px';
      }

      $container.css('top', top);
    },
    _scrollUp: function(step) {
      var $el = this.$el,
          $container = $el.find('.store-info'),
          top = $container.position().top;
      top += step;
      if (top > 0) { top = 0; }
      $container.css('top', top);
    },
    _scrollDown: function(step) {
      var $el = this.$el,
          $container = $el.find('.store-info'),
          top = $container.position().top,
          parentHeight = $container.parent().height(),
          height = $container.height(),
          diff = height - parentHeight;

      top -= step;
      if (Math.abs(top) > diff) {
        top = '-' + diff + 'px';
      }

      $container.css('top', top);
    },
    scroll: function(event) {
      if (event.preventDefault) {
        event.preventDefault();
      }
      event.returnValue = false;

      var delta = event.originalEvent.wheelDelta;

      var _step = this._step * 2,
          $el = this.$el,
          $container= $el.find('.store-info'),
          top = $container.position().top;

      if (delta > 0) {
        this._scrollUp(this._step * 2);
      } else {
        this._scrollDown(this._step * 2);
      }
    },
    scrollUpClick: function() {
      if (window.mobilecheck() === false) {
        return false;
      }
      this._scrollUp(this._step);
      return false;
    },
    scrollUpStart: function() {
      if (window.mobilecheck() === true) {
        return false;
      }
      var _self = this;
      this._scrollUpTimer = setInterval(
        function() { _self._scrollUp.call(_self, _self._step); },
        100
      );
    },
    scrollUpEnd: function() {
      clearInterval(this._scrollUpTimer);
    },
    scrollDownClick: function() {
      if (window.mobilecheck() === false) {
        return false;
      }
      this._scrollDown(this._step);
      return false;
    },
    scrollDownStart: function() {
      if (window.mobilecheck() === true) {
        return false;
      }
      var _self = this;
      this._scrollDownTimer = setInterval(
        function() { _self._scrollDown.call(_self, _self._step); },
        100
      );
    },
    scrollDownEnd: function() {
      clearInterval(this._scrollDownTimer);
    },
    getCurrentPosition: function() {
      return this.position;
    },
    setCurrentPosition: function(position) {
      this.position = position;
      return this;
    },
    render: function(stores) {
      if (typeof stores === 'undefined' || stores == null) {
        stores = this.stores;
      } else {
        this.stores = stores;
      }

      stores = _.sortBy(stores, function(store){
        if (store.get('is_flag_store') == '1') {
          return '-0';
        }

        if (store.get('is_repeat_store') == '1') {
          return '-1';
        }

        return store.get('name');
      });

      var data = { stores: stores },
          _self = this;
      this.currentStore = stores[0].get('entity_id');
      // this._event.trigger('store:change');
      data.currentStore = this.currentStore;

      data.distance = function(store) {
        if (typeof _self.position === 'undefined') {
          return '...';
        }
        var d = Math.greatCircleDistance(
          parseFloat(store.get('latitude')), parseFloat(store.get('longitude')),
          _self.position.coords.latitude, _self.position.coords.longitude
        );

        if (d < 1000) {
          return d.toFixed(0) + 'm';
        } else {
          return (d / 1000).toFixed(2) + 'km';
        }
      };

      var $content = $(this.template(data));

      this.$el.html($content);

      // swipe event on mobile device
      if (window.mobilecheck() === true) {
        var $container = $content.find('.store-info'),
            container = $container.get(0),
            clickElement = null,
            isMoved = false,
            sTop = $container.position().top;
            yDown = null;

        var onTouchMove = function(event) {
          event.preventDefault();
          isMoved = true;

          if (!yDown) {
            return;
          }

          var yUp = event.touches[0].screenY,
              yDiff = yDown - yUp;

          _self._scrollTo(sTop - yDiff);
        };

        var onTouchEnd = function(event) {
          if (isMoved === false) {
            $(clickElement).click();
          }
          
          sTop = $container.position().top;
          container.removeEventListener('touchmove', onTouchMove);
          container.removeEventListener('touchend', onTouchEnd);
        };

        container.addEventListener('touchstart', function(event) {
          event.preventDefault();
          clickElement = event.target;
          yDown = event.touches[0].screenY;
          isMoved = false;
          
          container.addEventListener('touchmove', onTouchMove);
          container.addEventListener('touchend', onTouchEnd);
        }, false);
      }
    }
  });

  ecif.View.StoreMapView = Backbone.View.extend({
    events: {
      'click .btn-store-info': 'storeContact',
    },
    storeContact: function(event){
      this.storeId =  $(event.currentTarget).parent().data('id');
      this.contactView.setStores(this.storeId).render();
      return false;
    },
    initialize: function(options) {
      options || (options = {});
      this.isGoogleMapInit = false;
      this.storeId = -1;
      this.markers = [];
      this.infoWindows = [];
      this.needRenderStores = true;
      this.image = MAP_MARKER_ICON;
      this.template = _.template(options.template.html());
    },
    setCurrentStore: function(storeId,click) {
      this.storeId = parseInt(storeId);
      this.click = click;
      return this;
    },
    setStores: function(stores) {
      stores || (stores = []);
      this.stores = stores;
      this.needRenderStores = true;
      return this;
    },
    initGoogleMap: function(container) {
      var mapProp = {
        center: new google.maps.LatLng(51.508742,-0.120850),
        zoom: 7,
        zoomControl:true,
        zoomControlOptions: {
          style:google.maps.ZoomControlStyle.SMALL
        },
        mapTypeId:google.maps.MapTypeId.ROADMAP
      };

      this._map = new google.maps.Map(container, mapProp);
    },
    render: function(stores, currentStore) {
      var _self = this;

      // init google map
      if (this.isGoogleMapInit === false) {
        this.initGoogleMap(this.$el.find('#googleMap').get(0));
        this.isGoogleMapInit = true;
      }

      _.each(this.infoWindows, function(_window) {
        _window.close();
      });

      // refresh markers
      if (_.isNull(stores) === false && _.isUndefined(stores) === false) {
        // clear marker
        _.each(this.markers, function(marker) {
          marker.setMap(null);
        });
        this.markers = [];
        this.infoWindows = [];

        // add markers, recalculate center, recalculate zoom
        var _x = 0,
            _y = 0;
        _.each(stores, function(store) {
          var marker = new google.maps.Marker({
                    position: new google.maps.LatLng(store.get('latitude'), store.get('longitude')),
                    map: _self._map,
                    icon: _self.image
                  });
          marker.storeId = store.get('id');
          _self.markers.push(marker);

          // set info window
          var info = { store: store },
              markerContent = _self.template(info);

          var options = {
                content: markerContent,
                disableAutoPan: false,
                maxWidth: 0,
                maxHeight: "400px",
                pixelOffset: new google.maps.Size(-189, 0),
                zIndex: null,
                boxStyle: { opacity: 1, width: "15px" },
                closeBoxMargin: "4px -382px 0 0",
                closeBoxURL: MAP_CLOSE_ICON,
                isHidden: false,
                pane: "floatPane",
                enableEventPropagation: false
              },
              infoWindow = new InfoBox(options);

          google.maps.event.addListener(marker, 'click', function() {
            _.each(_self.infoWindows, function(_window) {
              _window.close();
            });
            infoWindow.open(_self._map, marker);
            _self._map.panTo(new google.maps.LatLng(store.get('latitude'), store.get('longitude')));
          });
          infoWindow.storeId = store.get('id');
          _self.infoWindows.push(infoWindow);

          _x += parseFloat(store.get('latitude'));
          _y += parseFloat(store.get('longitude'));
        });

        _x /= stores.length;
        _y /= stores.length;
        this._map.panTo(new google.maps.LatLng(_x, _y));

        var _d = 0;
        _.each(stores, function(store) {
          var x = parseFloat(store.get('latitude')),
              y = parseFloat(store.get('longitude')),
              d = (x - _x) * (x - _x) + (y - _y) * (y - _y);
          if (d > _d) {
            _d = d;
          }
        });
        this._map.setZoom(12);
      }

      if (_.isNull(currentStore) === false && _.isUndefined(currentStore) === false) {
        var _x = currentStore.get('latitude'),
            _y = currentStore.get('longitude');
        this._map.panTo(new google.maps.LatLng(_x, _y));
        this._map.setZoom(14);
        for (var i = 0, l = this.infoWindows.length; i < l; i++) {
          if (this.infoWindows[i].storeId == currentStore.get('id')) {
            this.infoWindows[i].open(this._map, this.markers[i]);
          }
        }
      }

      var path = window.location.pathname;
      if (path.indexOf('detail') > 0) {
        setTimeout(function() {
          $('#googleMap .btn-store-info').click();
        }, 1000);
      }
    }
  });

  ecif.View.StoreDetailView = Backbone.View.extend({
    events: {
      'click .btn-back': 'back',
      'click .btn-direction': 'direction',
    },
    initialize: function(options) {
      options || (options = {});
      this.template = _.template(options.template.html());
      this._event = options._event;
      this._step = 20;
    },
    back: function() {
      this._event.trigger('store:list');
      return false;
    },
    direction: function(event) {
      $(event.target).closest('form').submit();
      return false;
    },
    render: function(store) {
      var data = { store: store };
      this.$el.html(this.template(data));
      this.store = store;

      var $container = this.$el.find('.map-holder');
      var mapProp = {
        center: new google.maps.LatLng(store.get('latitude'),store.get('longitude')),
        zoom: 15,
        zoomControl:true,
        zoomControlOptions: {
          style:google.maps.ZoomControlStyle.SMALL
        },
        mapTypeId:google.maps.MapTypeId.ROADMAP
      };

      this._map = new google.maps.Map($container.get(0), mapProp);

      var marker = new google.maps.Marker({
                    position: new google.maps.LatLng(store.get('latitude'), store.get('longitude')),
                    map: this._map,
                    icon: MAP_MARKER_ICON
                  });
    }
  });

  var StorelocatorRouter = Backbone.Router.extend({
    routes: {
      "": "view",
      ":store/": "view",
      ":store/:detail/": "view"
    }
  });

  $(function() {

    var storelocator_router = new StorelocatorRouter;

    localStorage.clear();
    var storeCollection = new ecif.Collection.StoreCollection();
    storeCollection.fetch();
    storeCollection.update($.parseJSON(storeJSON));

    var countryCollection = new ecif.Collection.CountryCollection();
    countryCollection.fetch();
    countryCollection.update($.parseJSON(countryJSON));
    countryCollection.comparator = 'name';
    countryCollection.sort();

    root._event = _.extend({}, Backbone.Events);
    var filterView = new ecif.View.StoreFilterView({
                        collection: storeCollection,
                        countries: countryCollection,
                        defaultCountry: storeCode,
                        router: storelocator_router,
                        template: $('#tpl-store-filter'),
                        el: $('.block-filter'),
                        _event: root._event
                      });

    var listView = new ecif.View.StoreListView({
      collection: storeCollection,
      collect: function() { return filterView.getStores(); },
      router: storelocator_router,
      template: $('#tpl-store-list'),
      el: $('.block-list'),
      _event: root._event
    });

    var mapView = new ecif.View.StoreMapView({
      template: $('#tpl-store-marker'),
      el: $('.block-store-detail'),
      _event: root._event
    });

    var detailView = new ecif.View.StoreDetailView({
      template: $('#tpl-store-detail'),
      el: $('.scene.store-info'),
      _event: root._event
    });

    storelocator_router.on('route:view', function(query, detail) {
      if (typeof query !== 'undefined') {
        if (window.is_int(query) === true) {
          // route by store id
          store = storeCollection.where({
            entity_id: query
          });
        } else {
          // route by store
          store = storeCollection.where({
            url_key: query
          });
        }

        if (store.length > 0) {
          filterView.set('store', store[0]).render();
          root._event.trigger('store:change', store[0]);
        } else {
          // try country
          store = storeCollection.where({
            countryurl: query
          });

          if (store.length > 0) {
            filterView.set('store', store[0]).render();
            root._event.trigger('store:change', store[0]);
          } else {
            // try city
            store = query.toLowerCase();
            var _store = storeCollection.find(function(_store) {
              
              if (_store.get('city').toLowerCase() == store) {
                return true;
              }

              return false;
            });
          }

          if (typeof _store !== 'undefined') {
            filterView.set('store', _store).render();
            root._event.trigger('store:change', store[0]);
          }
        }
      }
    });

    // global events
    root._event.on('filter:change', function() {
      var stores = filterView.getStores();
      listView.render(stores);
      mapView.render(stores);
    });

    root._event.on('store:change', function(store) {
      mapView.render(null, store);
    });

    root._event.on('store:detail', function(store) {
      $('.scene.store-listing').removeClass('active');
      $('.scene.store-info').addClass('active');
      detailView.render(store);
    });

    root._event.on('store:list', function(store) {
      $('.scene.store-listing').addClass('active');
      $('.scene.store-info').removeClass('active');
    });

    root._event.on('store:nearest', function() {
      var position = listView.getCurrentPosition(),
          stores = filterView.nearest(position.coords.latitude, position.coords.longitude);
      listView.render(stores);
      mapView.render(stores);
    });

    root._event.on('position:changed', function(position) {
      listView.setCurrentPosition(position).render();
    });

    root._event.on('popup:storeinfo', function(id) {
      var store = storeCollection.where({'entity_id': id.toString()})[0];
      root._event.trigger('store:detail', store);
    });

    root._event.on('app:loaded', function() {
      // $('html, body').animate({scrollTop: $('.breadcrumbs').offset().top + 25 + 'px'}, 500);
      filterView.render();
    });

    if (navigator.geolocation) {
      navigator.geolocation.watchPosition(function(position) {
        root._event.trigger('position:changed', position);
      });

      $('.btn-find-nearest-store').on('click', function() {
        root._event.trigger('store:nearest');
      });
    } else {
      $('.block-find-nearest-store').addClass('disabled');
    }

    root._event.trigger('app:loaded');
    Backbone.history.start({ 
      pushState: true,
      root: approot
    });

    var path = window.location.pathname.split('/');
    var query = (path[path.length-1] == '') ? path[path.length-2] : path[path.length-1];

    if (query == 'detail') {
      var idx = (path[path.length-1] == '') ? path.length-2 : path.length-1;
      query = path[idx-1] + '/' + path[idx];
    }

    if (['storelocator', 'store_locator', 'shopsuche', 'trouver-un-magasin.html'].indexOf(query) == -1) {
      storelocator_router.navigate(
        '/' + query + '/',
        { trigger: true }
      );
    }





  });
})(this);
