// authController.js
const bcrypt = require('bcryptjs');
const jwt = require('jsonwebtoken');

const generateToken = (user) =>
    jwt.sign({ id: user.user_id, role: user.role_name }, process.env.JWT_SECRET, { expiresIn: '24h' });

exports.login = async (req, res, next) => {
    try {
        const { email, password } = req.body;
        // TODO: query DB for user by email, verify password hash
        // const user = await User.findByEmail(email);
        // if (!user || !await bcrypt.compare(password, user.password_hash)) {
        //     return res.status(401).json({ error: 'Invalid credentials' });
        // }
        // const token = generateToken(user);
        // res.json({ token, user: { id: user.user_id, name: `${user.first_name} ${user.last_name}`, role: user.role_name } });
        res.json({ token: 'demo-token', user: { id: 1, name: 'John Doe', initials: 'JD', role: 'Manager' } });
    } catch (err) { next(err); }
};

exports.googleCallback = async (req, res, next) => {
    try {
        const token = generateToken(req.user);
        res.redirect(`${process.env.FRONTEND_URL}/dashboard.html?token=${token}`);
    } catch (err) { next(err); }
};

exports.logout = (req, res) => res.json({ message: 'Logged out successfully' });
