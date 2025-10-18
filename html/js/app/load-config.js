let Config = {};

Config.url = './config.json';
Config.storage = {};
Config.isInit = false;

Config.init = function () {
  return new Promise(function (resolve, reject) {
    $.ajax({
      url: Config.url + '?' + Date(),
      success: function (data) {
        $.each(data, Config.set);
        Config.isInit = true;
        resolve();
      },
      error: function () {
        alert('There is a problem with configuration load');
        reject();
      },
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
