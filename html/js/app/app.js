let App = {};

App.isReady = false;
App.waitingStack = [];

App.init = function () {
  let promises = [];

  promises.push(Config.init());

  Promise.all(promises).then(function () {
    for (let i = 0; i < App.waitingStack.length; i++) {
      App.waitingStack[i]();
    }

    App.waitingStack = [];
  });
};

App.ready = function (callback) {
  if (typeof callback !== 'function') {
    return;
  }

  if (App.isReady) {
    callback();
  } else {
    App.waitingStack.push(callback);
  }
};

App.getQuery = function (name) {
  name = name.replace(/[\[\]]/g, '\\$&');

  let url = window.location.href;
  let regex = new RegExp('[?&]' + name + '(=([^&#]*)|&|#|$)');
  let results = regex.exec(url);

  if (!results) {
    return null;
  }

  if (!results[2]) {
    return '';
  }

  return decodeURIComponent(results[2].replace(/\+/g, ' '));
};

$(App.init);
