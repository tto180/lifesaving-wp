/** This file is part of the LearnDash plugin and was generated automatically */
(()=>{function e(t){return e="function"==typeof Symbol&&"symbol"==typeof Symbol.iterator?function(e){return typeof e}:function(e){return e&&"function"==typeof Symbol&&e.constructor===Symbol&&e!==Symbol.prototype?"symbol":typeof e},e(t)}function t(e,t){var n=Object.keys(e);if(Object.getOwnPropertySymbols){var o=Object.getOwnPropertySymbols(e);t&&(o=o.filter((function(t){return Object.getOwnPropertyDescriptor(e,t).enumerable}))),n.push.apply(n,o)}return n}function n(e){for(var n=1;n<arguments.length;n++){var r=null!=arguments[n]?arguments[n]:{};n%2?t(Object(r),!0).forEach((function(t){o(e,t,r[t])})):Object.getOwnPropertyDescriptors?Object.defineProperties(e,Object.getOwnPropertyDescriptors(r)):t(Object(r)).forEach((function(t){Object.defineProperty(e,t,Object.getOwnPropertyDescriptor(r,t))}))}return e}function o(t,n,o){return(n=function(t){var n=function(t,n){if("object"!=e(t)||!t)return t;var o=t[Symbol.toPrimitive];if(void 0!==o){var r=o.call(t,n||"default");if("object"!=e(r))return r;throw new TypeError("@@toPrimitive must return a primitive value.")}return("string"===n?String:Number)(t)}(t,"string");return"symbol"==e(n)?n:n+""}(n))in t?Object.defineProperty(t,n,{value:o,enumerable:!0,configurable:!0,writable:!0}):t[n]=o,t}jQuery((function(e){function t(){window.DocsBotAI=window.DocsBotAI||{},DocsBotAI.init=function(e){return new Promise((function(t,n){var o=document.createElement("script");o.type="text/javascript",o.async=!0,o.src="https://widget.docsbot.ai/chat.js";var r=document.getElementsByTagName("script")[0];r.parentNode.insertBefore(o,r),o.addEventListener("load",(function(){var o;window.DocsBotAI.mount({id:e.id,supportCallback:e.supportCallback,identify:e.identify,options:e.options,signature:e.signature}),o=function(e){return new Promise((function(t){if(document.querySelector(e))return t(document.querySelector(e));var n=new MutationObserver((function(o){document.querySelector(e)&&(t(document.querySelector(e)),n.disconnect())}));n.observe(document.body,{childList:!0,subtree:!0})}))},o&&o("#docsbotai-root").then(t).catch(n)})),o.addEventListener("error",(function(e){n(e.message)}))}))},window.DocsBotAI.init({id:"yes2mjAljn0V5ndsWaOi/e5zKJJUaNT6B6v9adswa",options:{color:"#235af3",supportLink:"#",supportCallback:function(e){e.preventDefault(),window.DocsBotAI.unmount(),o(),Beacon("open")}}})}function o(){var e=arguments.length>0&&void 0!==arguments[0]?arguments[0]:{};!function(e,t,n){function o(){var e=t.getElementsByTagName("script")[0],n=t.createElement("script");n.type="text/javascript",n.async=!0,n.src="https://beacon-v2.helpscout.net",e.parentNode.insertBefore(n,e)}if(e.Beacon=n=function(t,n,o){e.Beacon.readyQueue.push({method:t,options:n,data:o})},n.readyQueue=[],"complete"===t.readyState)return o();e.attachEvent?e.attachEvent("onload",o):e.addEventListener("load",o,!1)}(window,document,window.Beacon||function(){}),e=n(n({},{docsEnabled:!1,messagingEnabled:!0}),e),Beacon("config",e),Beacon("init","1418fe60-cd03-4691-a765-66e6166f1695"),Beacon("on","close",(function(){Beacon("destroy"),t()}))}t(),e(document).on("submit","#search-form",(function(t){t.preventDefault();var n={};e.each(e(this).serializeArray(),(function(e,t){n[t.name]=t.value})),n.keyword.length>0&&(window.DocsBotAI.open(),window.DocsBotAI.addUserMessage(n.keyword,!0))})),e(document).on("click",".answers .item",(function(t){t.preventDefault(),window.DocsBotAI.unmount(),o({docsEnabled:!0,messagingEnabled:!1});var n=e(this).data("id");Beacon("open"),Beacon("navigate","/docs/search?query=category:"+n)}))}))})();