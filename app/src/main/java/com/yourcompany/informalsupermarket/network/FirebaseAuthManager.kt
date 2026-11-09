package com.yourcompany.informalsupermarket.network

import com.google.firebase.auth.FirebaseAuth

/**
 * Service helper providing Firebase Authentication (sign in, sign up, session management).
 */
object FirebaseAuthManager {
    private var auth: FirebaseAuth? = null

    init {
        try {
            auth = FirebaseAuth.getInstance()
        } catch (_: Exception) {
            // Graceful fallback if Firebase instance is unavailable offline
        }
    }

    fun getCurrentUserUid(): String? {
        return try {
            auth?.currentUser?.uid
        } catch (_: Exception) {
            null
        }
    }

    fun signInWithEmail(
        email: String,
        pass: String,
        onSuccess: (String?) -> Unit,
        onError: (String) -> Unit
    ) {
        val instance = auth
        if (instance == null) {
            onError("Firebase Auth unavailable")
            return
        }

        instance.signInWithEmailAndPassword(email.trim(), pass)
            .addOnCompleteListener { task ->
                if (task.isSuccessful) {
                    onSuccess(instance.currentUser?.uid)
                } else {
                    val msg = task.exception?.localizedMessage ?: "Firebase Sign-In failed"
                    onError(msg)
                }
            }
    }

    fun signUpWithEmail(
        email: String,
        pass: String,
        onSuccess: (String?) -> Unit,
        onError: (String) -> Unit
    ) {
        val instance = auth
        if (instance == null) {
            onError("Firebase Auth unavailable")
            return
        }

        instance.createUserWithEmailAndPassword(email.trim(), pass)
            .addOnCompleteListener { task ->
                if (task.isSuccessful) {
                    onSuccess(instance.currentUser?.uid)
                } else {
                    val msg = task.exception?.localizedMessage ?: "Firebase Registration failed"
                    onError(msg)
                }
            }
    }

    fun signOut() {
        try {
            auth?.signOut()
        } catch (_: Exception) {
        }
    }
}
