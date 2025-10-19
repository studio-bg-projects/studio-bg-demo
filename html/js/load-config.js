let Config = {};

Config.url = './config.json';
Config.storage = {};
Config.isInit = false;

Config.init = function () {
  return new Promise(function (resolve, reject) {
    let url = Config.url + '?' + Date.now();

    fetch(url, {
      cache: 'no-store',
    }).then(function (response) {
      if (!response.ok) {
        throw new Error('Config request failed');
      }

      return response.json();
    }).then(function (data) {
      Object.keys(data).forEach(function (key) {
        Config.set(key, data[key]);
      });

      Config.isInit = true;
      resolve();
    }).catch(function () {
      alert('There is a problem with configuration load');
      reject();
    });
  });
};

Config.get = function (key) {
  if (!Config.isInit) {
    throw new Error('Config is not initialized');
  }

  return Config.storage[key] ? Config.storage[key] : null;
};

Config.set = function (key, value) {
  Config.storage[key] = value;
};
