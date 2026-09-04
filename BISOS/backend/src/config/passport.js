// config/passport.js
const passport = require('passport');
const GoogleStrategy = require('passport-google-oauth20').Strategy;

passport.use(new GoogleStrategy({
    clientID: process.env.GOOGLE_CLIENT_ID,
    clientSecret: process.env.GOOGLE_CLIENT_SECRET,
    callbackURL: `${process.env.API_URL}/v1/auth/google/callback`
}, async (accessToken, refreshToken, profile, done) => {
    try {
        // TODO: find or create user by Google profile email
        const user = {
            user_id: 1,
            email: profile.emails[0].value,
            first_name: profile.name.givenName,
            last_name: profile.name.familyName,
            role_name: 'Clerk'
        };
        return done(null, user);
    } catch (err) {
        return done(err, null);
    }
}));

module.exports = passport;
