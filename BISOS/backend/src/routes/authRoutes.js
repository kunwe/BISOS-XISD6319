// authRoutes.js
const express = require('express');
const router = express.Router();
const { login, logout, googleCallback } = require('../controllers/authController');
const passport = require('passport');

router.post('/login', login);
router.post('/logout', logout);
router.get('/google', passport.authenticate('google', { scope: ['email', 'profile'] }));
router.get('/google/callback', passport.authenticate('google', { session: false }), googleCallback);

module.exports = router;
