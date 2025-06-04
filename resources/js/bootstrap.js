import axios from 'axios';
import 'jkanban/dist/jkanban.min.css';

axios.defaults.withCredentials = true;                       // envia cookies de sessão
axios.defaults.headers.common['Accept'] = 'application/json';
axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
