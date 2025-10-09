<template>
    <v-app>
        <v-main>
            <v-container fluid class="pa-0 fill-height">
                <v-row no-gutters class="fill-height">
                    <!-- Left Panel - Dark Background -->
                    <v-col cols="12" md="5" class="d-none d-md-flex">
                        <div class="left-panel fill-height d-flex flex-column justify-space-between pa-8">
                            <!-- Logo -->
                            <div class="d-flex align-center">
                                <div class="logo-icon mr-3">
                                    <v-icon size="32" color="white">mdi-food</v-icon>
                                </div>
                                <h2 class="text-h5 font-weight-bold text-white">Food Ordering System</h2>
                            </div>

                            <!-- Testimonial -->
                            <div class="testimonial">
                                <blockquote class="text-h6 text-white mb-4 font-weight-light">
                                    "This platform has revolutionized our restaurant operations and helped us serve
                                    customers faster than ever before."
                                </blockquote>
                                <div class="text-white text-subtitle-2">
                                    Food Ordering System Team
                                </div>
                            </div>
                        </div>
                    </v-col>

                    <!-- Right Panel - Login Form -->
                    <v-col cols="12" md="7" class="d-flex align-center justify-center pa-8">
                        <div class="login-form-container" style="max-width: 400px; width: 100%;">

                            <!-- Login Form -->
                            <div class="mb-8">
                                <h1 class="text-h4 font-weight-bold text-grey-darken-3 mb-2">
                                    Login to your account
                                </h1>
                                <p class="text-grey-darken-1 text-subtitle-1">
                                    Enter your email below to login to your account
                                </p>
                            </div>

                            <v-form @submit.prevent="submit">
                                <!-- Email Field -->
                                <div class="mb-4">
                                    <label class="text-grey-darken-3 text-subtitle-2 font-weight-medium mb-2 d-block">
                                        Email
                                    </label>
                                    <v-text-field v-model="form.email" type="email" variant="outlined"
                                        :error-messages="form.errors.email" placeholder="Enter your email"
                                        class="custom-input" hide-details="auto" required />
                                </div>

                                <!-- Password Field -->
                                <div class="mb-4">
                                    <label class="text-grey-darken-3 text-subtitle-2 font-weight-medium mb-2 d-block">
                                        Password
                                    </label>
                                    <v-text-field v-model="form.password" :type="showPassword ? 'text' : 'password'"
                                        variant="outlined" :error-messages="form.errors.password"
                                        placeholder="Enter your password" class="custom-input" hide-details="auto"
                                        :append-inner-icon="showPassword ? 'mdi-eye' : 'mdi-eye-off'"
                                        @click:append-inner="showPassword = !showPassword" required />
                                </div>

                                <!-- Remember Me -->
                                <div class="mb-6">
                                    <v-checkbox v-model="form.remember" label="Remember me" hide-details
                                        color="grey-darken-3" />
                                </div>

                                <!-- Sign In Button -->
                                <v-btn type="submit" color="grey-darken-3" size="large" block :loading="form.processing"
                                    class="mb-4 text-white font-weight-medium" elevation="0">
                                    Sign in
                                </v-btn>

                                <!-- Forgot Password -->
                                <div class="text-center">
                                    <a href="#" class="text-grey-darken-1 text-decoration-none text-subtitle-2">
                                        Forgot your password?
                                    </a>
                                </div>
                            </v-form>

                            <!-- Register Link -->
                            <v-divider class="my-6" />
                            <div class="text-center">
                                <span class="text-grey-darken-1 text-subtitle-2">
                                    Don't have an account?
                                </span>
                                <Link
                                    href="/register"
                                    class="text-grey-darken-3 text-decoration-none ml-1 font-weight-medium">
                                    Sign up
                                </Link>
                            </div>

                            <!-- Demo Accounts -->
                            <v-card v-if="!form.processing" variant="outlined" class="mt-6 pa-4" color="blue-lighten-5">
                                <v-card-title class="text-subtitle-1 pa-0 mb-2">
                                    <v-icon left color="blue">mdi-information</v-icon>
                                    Demo Accounts
                                </v-card-title>
                                <v-card-text class="pa-0">
                                    <div class="text-caption mb-2">
                                        <strong>User:</strong> user@test.com / password<br>
                                        <strong>Admin:</strong> admin@test.com / password
                                    </div>
                                    <v-btn size="small" color="blue" variant="outlined"
                                        @click="fillDemoCredentials('user')">
                                        User Demo
                                    </v-btn>
                                    <v-btn size="small" color="blue" variant="outlined"
                                        @click="fillDemoCredentials('admin')" class="ml-2">
                                        Admin Demo
                                    </v-btn>
                                </v-card-text>
                            </v-card>
                        </div>
                    </v-col>
                </v-row>
            </v-container>
        </v-main>
    </v-app>
</template>

<script setup>
import { ref } from 'vue';
import { useForm, Link } from '@inertiajs/vue3';

    const showPassword = ref(false);

    const form = useForm({
        email: '',
        password: '',
        remember: false,
    });

    const submit = () => {
        form.post('/login', {
            onFinish: () => form.reset('password'),
        });
    };

    const fillDemoCredentials = (role) => {
        const credentials = {
            user: { email: 'user@test.com', password: 'password' },
            admin: { email: 'admin@test.com', password: 'password' },
        };

        const creds = credentials[role];
        form.email = creds.email;
        form.password = creds.password;
    };
</script>

<style scoped>
.left-panel {
    background: linear-gradient(135deg, #2c3e50 0%, #34495e 100%);
    position: relative;
    overflow: hidden;
}

.left-panel::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-image: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grain" width="100" height="100" patternUnits="userSpaceOnUse"><circle cx="25" cy="25" r="1" fill="rgba(255,255,255,0.1)"/><circle cx="75" cy="75" r="1" fill="rgba(255,255,255,0.05)"/><circle cx="50" cy="10" r="0.5" fill="rgba(255,255,255,0.08)"/><circle cx="10" cy="60" r="0.8" fill="rgba(255,255,255,0.06)"/><circle cx="90" cy="40" r="0.6" fill="rgba(255,255,255,0.04)"/></pattern></defs><rect width="100" height="100" fill="url(%23grain)"/></svg>');
    opacity: 0.3;
    z-index: 0;
}

.left-panel>* {
    position: relative;
    z-index: 1;
}

.logo-icon {
    width: 40px;
    height: 40px;
    background: rgba(255, 255, 255, 0.1);
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.testimonial blockquote {
    line-height: 1.6;
    font-style: italic;
}

.custom-input :deep(.v-field) {
    background-color: #f8f9fa;
    border-radius: 8px;
}

.custom-input :deep(.v-field__outline) {
    border-color: #e0e0e0;
}

.custom-input :deep(.v-field--focused .v-field__outline) {
    border-color: #424242;
    border-width: 2px;
}

.custom-input :deep(.v-field__input) {
    padding: 12px 16px;
}

/* Mobile responsive */
@media (max-width: 959px) {
    .left-panel {
        display: none !important;
    }
}
</style>
