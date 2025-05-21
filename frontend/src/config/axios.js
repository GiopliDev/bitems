import axios from 'axios';

const instance = axios.create({
    baseURL: '/',
    headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json'
    }
});

// Interceptor per gestire gli errori
instance.interceptors.response.use(
    response => response,
    error => {
        console.error('Errore di risposta:', error);
        return Promise.reject(error);
    }
);

export default instance; 