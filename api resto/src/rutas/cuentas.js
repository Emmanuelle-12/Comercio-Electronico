const { Router } = require('express');
const router = Router();

const cuentas = require('../registros.json');

router.get('/', (req, res) => {
    res.json(cuentas);
})
module.exports = router;