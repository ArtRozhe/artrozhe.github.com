(function(global) {
  'use strict';
  /**
   * Turns on a smooth transition after the page loads
  **/
  var transAfterPageLoad = function() {
    document.querySelector('.preload').classList.remove('preload');
  }

  window.onload = transAfterPageLoad;
})(this);
