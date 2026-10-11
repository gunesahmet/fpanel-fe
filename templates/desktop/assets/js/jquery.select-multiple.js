/*
* SelectMultiple v0.1
* Copyright (c) 2015 krazedkrish( Shalil Awaley )
*
* This program is free software. It comes without any warranty, to
* the extent permitted by applicable law. You can redistribute it
* and/or modify it under the terms of the MIT LICENSE.
* See https://en.wikipedia.org/wiki/MIT_License for more details.
*/

!function ($) {

  "use strict";


 /* SELECTMULTIPLE CLASS DEFINITION
  * ====================== */

  var SelectMultiple = function (element, options) {
    this.options = options;
    this.$element = $(element);
    this.$container = $('<div/>', { 'class': "jms-container" });
    this.$selectableContainer = $('<div/>', { 'class': 'jms-selectable' });
    this.$selectableUl = $('<ul/>', { 'class': "jms-list", 'tabindex' : '-1' });
    this.scrollTo = 0;
    this.elemsSelector = 'li';
  };

  SelectMultiple.prototype = {
    constructor: SelectMultiple,

    init: function(){
      var that = this,
          ms = this.$element;

      if (ms.next('.jms-container').length === 0){
        ms.css({ position: 'absolute', left: '-9999px' });
        ms.attr('id', ms.attr('id') ? ms.attr('id') : Math.ceil(Math.random()*1000)+'selectmultiple');
        this.$container.attr('id', 'jms-'+ms.attr('id'));
        this.$container.addClass(that.options.cssClass);
        ms.find('option').each(function(){
          that.generateLisFromOption(this);
        });


        if (that.options.selectableHeader){
          that.$selectableContainer.append(that.options.selectableHeader);
        }
        that.$selectableContainer.append(that.$selectableUl);
        if (that.options.selectableFooter){
          that.$selectableContainer.append(that.options.selectableFooter);
        }


        that.$container.append(that.$selectableContainer);
        ms.after(that.$container);

        that.activeMouse(that.$selectableUl);
        that.activeKeyboard(that.$selectableUl);

        var action = that.options.dblClick ? 'dblclick' : 'click';

        that.$selectableUl.on(action, '.jms-elem-selectable', function(){
          that.select($(this).data('jms-value'));
        });


        ms.on('focus', function(){
          that.$selectableUl.focus();
        })
      }

      var selectedValues = ms.find('option:selected').map(function(){ return $(this).val(); }).get();
      that.select(selectedValues, 'init');

      if (typeof that.options.afterInit === 'function') {
        that.options.afterInit.call(this, this.$container);
      }
    },

    'generateLisFromOption' : function(option, index, $container){
      var that = this,
          ms = that.$element,
          attributes = "",
          $option = $(option);

      for (var cpt = 0; cpt < option.attributes.length; cpt++){
        var attr = option.attributes[cpt];

        if(attr.name !== 'value' && attr.name !== 'disabled'){
          attributes += attr.name+'="'+attr.value+'" ';
        }
      }
      var selectableLi = $('<li '+attributes+'><span>'+that.escapeHTML($option.text())+'</span><span class="pull-right jms-elem-selected">✔</span></li>'),
          selectedLi = selectableLi.clone(),
          value = $option.val(),
          elementId = that.sanitize(value);

      selectableLi.children('.jms-elem-selected').hide();

      selectableLi
        .data('jms-value', value)
        .addClass('jms-elem-selectable')
        .attr('id', elementId+'-selectable');


      if ($option.prop('disabled') || ms.prop('disabled')){
        selectableLi.addClass(that.options.disabledClass);
      }

      var $optgroup = $option.parent('optgroup');

      if ($optgroup.length > 0){
        var optgroupLabel = $optgroup.attr('label'),
            optgroupId = that.sanitize(optgroupLabel),
            $selectableOptgroup = that.$selectableUl.find('#optgroup-selectable-'+optgroupId);

        if ($selectableOptgroup.length === 0){
          var optgroupContainerTpl = '<li class="jms-optgroup-container"></li>',
              optgroupTpl = '<ul class="jms-optgroup"><li class="jms-optgroup-label"><span>'+optgroupLabel+'</span></li></ul>';

          $selectableOptgroup = $(optgroupContainerTpl);
          $selectableOptgroup.attr('id', 'optgroup-selectable-'+optgroupId);
          $selectableOptgroup.append($(optgroupTpl));
          if (that.options.selectableOptgroup){
            $selectableOptgroup.find('.jms-optgroup-label').on('click', function(){
              var values = $optgroup.children(':not(:selected, :disabled)').map(function(){ return $(this).val() }).get();
              that.select(values);
            });
          }
          that.$selectableUl.append($selectableOptgroup);
        }
        index = index == undefined ? $selectableOptgroup.find('ul').children().length : index + 1;
        selectableLi.insertAt(index, $selectableOptgroup.children());
      } else {
        index = index == undefined ? that.$selectableUl.children().length : index;

        selectableLi.insertAt(index, that.$selectableUl);
      }
    },

    'addOption' : function(options){
      var that = this;

      if (options.value !== undefined && options.value !== null){
        options = [options];
      }
      $.each(options, function(index, option){
        if (option.value !== undefined && option.value !== null &&
            that.$element.find("option[value='"+option.value+"']").length === 0){
          var $option = $('<option value="'+option.value+'">'+option.text+'</option>'),
              index = parseInt((typeof option.index === 'undefined' ? that.$element.children().length : option.index)),
              $container = option.nested == undefined ? that.$element : $("optgroup[label='"+option.nested+"']")

          $option.insertAt(index, $container);
          that.generateLisFromOption($option.get(0), index, option.nested);
        }
      })
    },

    'escapeHTML' : function(text){
      return $("<div>").text(text).html();
    },

    'activeKeyboard' : function($list){
      var that = this;

      $list.on('focus', function(){
        $(this).addClass('ms-focus');
      })
      .on('blur', function(){
        $(this).removeClass('ms-focus');
      })
      .on('keydown', function(e){
        switch (e.which) {
          case 40:
          case 38:
            e.preventDefault();
            e.stopPropagation();
            that.moveHighlight($(this), (e.which === 38) ? -1 : 1);
            return;
          case 37:
          case 39:
            e.preventDefault();
            e.stopPropagation();
            that.switchList($list);
            return;
          case 9:
            if(that.$element.is('[tabindex]')){
              e.preventDefault();
              var tabindex = parseInt(that.$element.attr('tabindex'), 10);
              tabindex = (e.shiftKey) ? tabindex-1 : tabindex+1;
              $('[tabindex="'+(tabindex)+'"]').focus();
              return;
            }else{
              if(e.shiftKey){
                that.$element.trigger('focus');
              }
            }
        }
        if($.inArray(e.which, that.options.keySelect) > -1){
          e.preventDefault();
          e.stopPropagation();
          that.selectHighlighted($list);
          return;
        }
      });
    },

    'moveHighlight': function($list, direction){
      var $elems = $list.find(this.elemsSelector),
          $currElem = $elems.filter('.jms-hover'),
          $nextElem = null,
          elemHeight = $elems.first().outerHeight(),
          containerHeight = $list.height(),
          containerSelector = '#'+this.$container.prop('id');

      $elems.removeClass('jms-hover');
      if (direction === 1){ // DOWN

        $nextElem = $currElem.nextAll(this.elemsSelector).first();
        if ($nextElem.length === 0){
          var $optgroupUl = $currElem.parent();

          if ($optgroupUl.hasClass('jms-optgroup')){
            var $optgroupLi = $optgroupUl.parent(),
                $nextOptgroupLi = $optgroupLi.next(':visible');

            if ($nextOptgroupLi.length > 0){
              $nextElem = $nextOptgroupLi.find(this.elemsSelector).first();
            } else {
              $nextElem = $elems.first();
            }
          } else {
            $nextElem = $elems.first();
          }
        }
      } else if (direction === -1){ // UP

        $nextElem = $currElem.prevAll(this.elemsSelector).first();
        if ($nextElem.length === 0){
          var $optgroupUl = $currElem.parent();

          if ($optgroupUl.hasClass('jms-optgroup')){
            var $optgroupLi = $optgroupUl.parent(),
                $prevOptgroupLi = $optgroupLi.prev(':visible');

            if ($prevOptgroupLi.length > 0){
              $nextElem = $prevOptgroupLi.find(this.elemsSelector).last();
            } else {
              $nextElem = $elems.last();
            }
          } else {
            $nextElem = $elems.last();
          }
        }
      }
      if ($nextElem.length > 0){
        $nextElem.addClass('jms-hover');
        var scrollTo = $list.scrollTop() + $nextElem.position().top -
                       containerHeight / 2 + elemHeight / 2;

        $list.scrollTop(scrollTo);
      }
    },

    'selectHighlighted' : function($list){
      var $elems = $list.find(this.elemsSelector),
          $highlightedElem = $elems.filter('.jms-hover').first();

      if ($highlightedElem.length > 0){
        if ($list.parent().hasClass('jms-selectable')){
          this.select($highlightedElem.data('jms-value'));
        } else {
          this.deselect($highlightedElem.data('jms-value'));
        }
        $elems.removeClass('jms-hover');
      }
    },

    'switchList' : function($list){
      $list.blur();
      this.$container.find(this.elemsSelector).removeClass('jms-hover');
      if ($list.parent().hasClass('jms-selectable')){
      } else {
        this.$selectableUl.focus();
      }
    },

    'activeMouse' : function($list){
      var that = this;

      $('body').on('mouseenter', that.elemsSelector, function(){
        $(this).parents('.jms-container').find(that.elemsSelector).removeClass('jms-hover');
        $(this).addClass('jms-hover');
      });

      $('body').on('mouseleave', that.elemsSelector, function () {
          $(this).parents('.jms-container').find(that.elemsSelector).removeClass('jms-hover');;
      });
    },

    'refresh' : function() {
      this.destroy();
      this.$element.selectMultiple(this.options);
    },

    'destroy' : function(){
      $("#jms-"+this.$element.attr("id")).remove();
      this.$element.css('position', '').css('left', '')
      this.$element.removeData('selectmultiple');
    },

    'select' : function(value, method){
      if (typeof value === 'string'){ value = [value]; }

      var that = this,
          ms = this.$element,
          msIds = $.map(value, function(val){ return(that.sanitize(val)); }),
          selectables = this.$selectableUl.find('#' + msIds.join('-selectable, #')+'-selectable').filter(':not(.'+that.options.disabledClass+')'),
          options = ms.find('option:not(:disabled)').filter(function(){ return($.inArray(this.value, value) > -1); });

      if (method === 'init'){
        selectables = this.$selectableUl.find('#' + msIds.join('-selectable, #')+'-selectable');
      }

      if (selectables.length > 0){
        selectables.addClass('jms-selected').children('.jms-elem-selected').show();

        if (method !== 'init' && options.prop('selected') == true) {
          that.deselect(value, method);
          return;
        }

        options.prop('selected', true);

        var selectableOptgroups = that.$selectableUl.children('.jms-optgroup-container');
        if (selectableOptgroups.length > 0){
          selectableOptgroups.each(function(){
            var selectablesLi = $(this).find('.jms-elem-selectable');
            if (selectablesLi.length === selectablesLi.filter('.jms-selected').length){
              $(this).find('.jms-optgroup-label').hide();
            }
          });

        }
        if (method !== 'init'){
          ms.trigger('change');
          if (typeof that.options.afterSelect === 'function') {
            that.options.afterSelect.call(this, value);
          }
        }
      }
    },

    'deselect' : function(value, method){
      if (typeof value === 'string'){ value = [value]; }

      var that = this,
          ms = this.$element,
          msIds = $.map(value, function(val){ return(that.sanitize(val)); }),
          selectables = this.$selectableUl.find('#' + msIds.join('-selectable, #')+'-selectable'),
          options = ms.find('option').filter(function(){ return($.inArray(this.value, value) > -1); });

      selectables.removeClass('jms-selected').children('.jms-elem-selected').hide();
      options.prop('selected', false);

      var selectableOptgroups = that.$selectableUl.children('.jms-optgroup-container');
      if (selectableOptgroups.length > 0){
        selectableOptgroups.each(function(){
          var selectablesLi = $(this).find('.jms-elem-selectable');
          if (selectablesLi.filter(':not(.jms-selected)').length > 0){
            $(this).find('.jms-optgroup-label').show();
          }
        });

      }
      if (method !== 'init'){
        ms.trigger('change');
        if (typeof that.options.afterDeselect === 'function') {
          that.options.afterDeselect.call(this, value);
        }
      }
    },

    'select_all' : function(){
      var ms = this.$element,
          values = ms.val();

      ms.find('option:not(":disabled")').prop('selected', true);
      this.$selectableUl.find('.jms-elem-selectable').filter(':not(.'+this.options.disabledClass+')').addClass('jms-selected').children('.jms-elem-selected').show();
      this.$selectableUl.find('.jms-optgroup-label').hide();
      ms.trigger('change');
      if (typeof this.options.afterSelect === 'function') {
        var selectedValues = $.grep(ms.val(), function(item){
          return $.inArray(item, values) < 0;
        });
        this.options.afterSelect.call(this, selectedValues);
      }
    },

    'deselect_all' : function(){
      var ms = this.$element,
          values = ms.val();

      ms.find('option').prop('selected', false);
      this.$selectableUl.find('.jms-elem-selectable').removeClass('jms-selected').children('.jms-elem-selected').hide();
      this.$selectableUl.find('.jms-optgroup-label').show();
      this.$selectableUl.focus();
      ms.trigger('change');
      if (typeof this.options.afterDeselect === 'function') {
        this.options.afterDeselect.call(this, values);
      }
    },

    sanitize: function(value){
      var hash = 0, i, character;
      if (value.length == 0) return hash;
      var ls = 0;
      for (i = 0, ls = value.length; i < ls; i++) {
        character  = value.charCodeAt(i);
        hash  = ((hash<<5)-hash)+character;
        hash |= 0; // Convert to 32bit integer
      }
      return hash;
    }
  };

  /* SELECTMULTIPLE PLUGIN DEFINITION
   * ======================= */

  $.fn.selectMultiple = function () {
    var option = arguments[0],
        args = arguments;

    return this.each(function () {
      var $this = $(this),
          data = $this.data('selectmultiple'),
          options = $.extend({}, $.fn.selectMultiple.defaults, $this.data(), typeof option === 'object' && option);

      if (!data){ $this.data('selectmultiple', (data = new SelectMultiple(this, options))); }

      if (typeof option === 'string'){
        data[option](args[1]);
      } else {
        data.init();
      }
    });
  };

  $.fn.selectMultiple.defaults = {
    keySelect: [32],
    selectableOptgroup: false,
    disabledClass : 'disabled',
    dblClick : false,
    keepOrder: false,
    cssClass: ''
  };

  $.fn.selectMultiple.Constructor = SelectMultiple;

  $.fn.insertAt = function(index, $parent) {
    return this.each(function() {
      if (index === 0) {
        $parent.prepend(this);
      } else {
        $parent.children().eq(index - 1).after(this);
      }
    });
}

}(window.jQuery);
