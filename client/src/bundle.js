import * as Klaro from 'klaro/dist/klaro-no-css';

import './style.scss';

// we assign the Klaro module to the window, so that we can access it in JS
window.klaro = Klaro;
// we set up Klaro with the config
Klaro.setup(window.klaroConfig);

const manager = Klaro.getManager();
window.klaroState = { ...manager.defaultConsents };
window.klaroUpdatingDependentState = false;

const watcher = {
  update(obj, nameo, data) {
    if (nameo !== 'consents') {
      return;
    }
    if (!window.klaroUpdatingDependentState) {
      window.klaroUpdatingDependentState = true;
      const { config: { services } } = obj;
      const oldState = { ...window.klaroState };
      const newState = { ...data };
      for (let i = 0; i < services.length; i += 1) {
        const { name, extDependsOn: dependencies } = services[i];
        if (dependencies && oldState[name] !== newState[name] && newState[name]) {
          for (let j = 0; j < dependencies.length; j += 1) {
            manager.updateConsent(dependencies[j], newState[name]);
            newState[dependencies[j]] = newState[name];
          }
        }
        if (dependencies) {
          for (let k = 0; k < dependencies.length; k += 1) {
            if (!newState[dependencies[k]]) {
              manager.updateConsent(name, newState[dependencies[k]]);
              newState[name] = newState[dependencies[k]];
            }
          }
        }
      }
      window.klaroState = newState;
      window.klaroUpdatingDependentState = false;
    }
  },
};
manager.watch(watcher);
