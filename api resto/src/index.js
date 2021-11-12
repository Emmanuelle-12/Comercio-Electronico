const express = require('express');
const app =  express();
const morgan = require('morgan');

//configuraciones
app.set('port', process.env.PORT || 3000);
app.set('json spaces', 2);

//middlewares
app.use(morgan('dev'));
app.use(express.urlencoded({extended: false}));//este metodo es para entender inputs de formularios
app.use(express.json());//este metodo permite al servidor entender los formator json

//rutas
app.use(require('./rutas/index'));
app.use('/api/cuentas',require('./rutas/cuentas'));

//iniciando el servidor
app.listen(app.get('port'), () => {
    console.log(`Server on port ${app.get('port')}`);
});