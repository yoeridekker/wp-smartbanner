(function(){function r(e,n,t){function o(i,f){if(!n[i]){if(!e[i]){var c="function"==typeof require&&require;if(!f&&c)return c(i,!0);if(u)return u(i,!0);var a=new Error("Cannot find module '"+i+"'");throw a.code="MODULE_NOT_FOUND",a}var p=n[i]={exports:{}};e[i][0].call(p.exports,function(r){var n=e[i][1][r];return o(n||r)},p,p.exports,r,e,n,t)}return n[i].exports}for(var u="function"==typeof require&&require,i=0;i<t.length;i++)o(t[i]);return o}return r})()({1:[function(require,module,exports){
"use strict";

var ios_enabled,
    android_enabled,
    ios = {
  apple_app_store_url: true,
  apple_app_store_icon_url: true,
  apple_app_store_tagline: false
},
    android = {
  google_play_store_url: true,
  google_play_store_icon_url: true,
  google_play_store_tagline: false
};

(function ($, undefined) {
  $("input").addClass("loaded");
  var show_on_ios = $("input#show_on_ios");
  ios_enabled = show_on_ios.is(":checked");
  var show_on_android = $("input#show_on_android");
  android_enabled = show_on_android.is(":checked");
  conditional_logic();
  show_on_ios.on("change", function () {
    ios_enabled = $(this).is(":checked");
    conditional_logic_ios();
  });
  show_on_android.on("change", function () {
    android_enabled = $(this).is(":checked");
    conditional_logic_android();
  });

  function conditional_logic() {
    conditional_logic_ios();
    conditional_logic_android();
  }

  function conditional_logic_ios() {
    for (var field in ios) {
      var $field = $("input#" + field);
      var $row = $field.parent().parent();

      if (ios[field]) {
        $field.attr("required", ios_enabled);
      }

      if (ios_enabled) {
        $row.show();
      } else {
        $row.hide();
      }
    }
  }

  function conditional_logic_android() {
    for (var field in android) {
      var $field = $("input#" + field);
      var $row = $field.parent().parent();

      if (android[field]) {
        $field.attr("required", android_enabled);
      }

      if (android_enabled) {
        $row.show();
      } else {
        $row.hide();
      }
    }
  }
})(jQuery);

},{}]},{},[1]);
