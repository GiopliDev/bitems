import axios from 'axios';

const instance = axios.create({
    baseURL: 'http://localhost/bitems',
    headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json'
    }
});

// Interceptor per gestire gli errori
instance.interceptors.response.use(
    response => response,
    error => {
        if (error.response) {
            // Gestione errori di risposta
            console.error('Errore di risposta:', error.response.data);
        } else if (error.request) {
            // La richiesta è stata fatta ma non c'è stata risposta
            console.error('Nessuna risposta ricevuta:', error.request);
        } else {
            // Errore nella configurazione della richiesta
            console.error('Errore nella richiesta:', error.message);
        }
        return Promise.reject(error);
    }
);

export default instance; 