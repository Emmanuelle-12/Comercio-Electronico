const { Router } = require('express');
const router = Router();

router.get('/test', (req, res) => {
    const datos = {
        "nombre": "Samuel",
        "website": "Samuelilloelpillo.com"
    };
    res.json(datos);
})

module.exports = router;