import * as Klaro from 'klaro/dist/klaro-no-css';

import './style.scss';

// we assign the Klaro module to the window, so that we can access it in JS
window.klaro = Klaro;
// we set up Klaro with the config
Klaro.setup(window.klaroConfig);
