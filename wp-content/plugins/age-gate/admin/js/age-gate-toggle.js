!function(t){function e(n){if(r[n])return r[n].exports;var o=r[n]={i:n,l:!1,exports:{}};return t[n].call(o.exports,o,o.exports,e),o.l=!0,o.exports}var r={};e.m=t,e.c=r,e.d=function(t,r,n){e.o(t,r)||Object.defineProperty(t,r,{configurable:!1,enumerable:!0,get:n})},e.n=function(t){var r=t&&t.__esModule?function(){return t.default}:function(){return t};return e.d(r,"a",r),r},e.o=function(t,e){return Object.prototype.hasOwnProperty.call(t,e)},e.p="/",e(e.s=33)}({33:function(t,e,r){"use strict";!function(t){t(window).on("agegatepassed",function(){var e=t("#wp-admin-bar-age-gate-toggle a"),r=e.attr("href");if(e.length){var n=r.replace("ag_switch=hide","ag_switch=show");e.attr("href",n).text(age_gate_toggle.show)}})}(jQuery)}});