<template>
    <v-app>
        <!-- Background with gradient -->
        <div class="auth-background">
            <v-main>
                <v-container fluid class="fill-height">
                    <v-row align="center" justify="center" class="min-height-screen">
                        <v-col cols="12" sm="10" md="8" lg="6" xl="5">
                            <!-- Main Card -->
                            <v-card elevation="24" class="auth-card pa-8" :class="{ 'shake': hasErrors }">
                                <!-- Header Section -->
                                <div class="text-center mb-8">
                                    <div class="auth-icon-wrapper mb-4">
                                        <v-icon size="48" color="primary" class="auth-icon">
                                            mdi-account-plus
                                        </v-icon>
                                    </div>
                                    <h1 class="text-h3 font-weight-bold text-primary mb-3">
                                        Join Our Community
                                    </h1>
                                    <p class="text-h6 text-grey-darken-1 mb-2">
                                        Create your account and start ordering delicious food
                                    </p>
                                    <v-divider class="mx-auto" style="width: 100px; border-color: #1976D2;" />
                                </div>

                                <!-- Progress Indicator -->
                                <div class="mb-6">
                                    <v-stepper v-model="currentStep" alt-labels flat class="stepper-custom">
                                        <v-stepper-header>
                                            <v-stepper-item :complete="currentStep > 1" :value="1" color="primary">
                                                <template v-slot:icon>
                                                    <v-icon>mdi-account</v-icon>
                                                </template>
                                                Personal Info
                                            </v-stepper-item>
                                            <v-divider />
                                            <v-stepper-item :complete="currentStep > 2" :value="2" color="primary">
                                                <template v-slot:icon>
                                                    <v-icon>mdi-shield-account</v-icon>
                                                </template>
                                                Account Type
                                            </v-stepper-item>
                                            <v-divider />
                                            <v-stepper-item :value="3" color="primary">
                                                <template v-slot:icon>
                                                    <v-icon>mdi-check-circle</v-icon>
                                                </template>
                                                Complete
                                            </v-stepper-item>
                                        </v-stepper-header>
                                    </v-stepper>
                                </div>

                                <!-- Registration Form -->
                                <v-form @submit.prevent="submit" ref="formRef">
                                    <!-- Step 1: Personal Information -->
                                    <div v-show="currentStep === 1" class="form-step">
                                        <h3 class="text-h5 font-weight-bold mb-6 text-center">
                                            Personal Information
                                        </h3>

                                        <v-row>
                                            <v-col cols="12" md="6">
                                                <v-text-field v-model="form.name" label="Full Name" variant="outlined"
                                                    :error-messages="form.errors.name"
                                                    prepend-inner-icon="mdi-account-outline" class="mb-4" required
                                                    :rules="nameRules" @blur="validateField('name')" />
                                            </v-col>
                                            <v-col cols="12" md="6">
                                                <v-text-field v-model="form.email" label="Email Address" type="email"
                                                    variant="outlined" :error-messages="form.errors.email"
                                                    prepend-inner-icon="mdi-email-outline" class="mb-4" required
                                                    :rules="emailRules" @blur="validateField('email')" />
                                            </v-col>
                                        </v-row>

                                        <v-row>
                                            <v-col cols="12" md="6">
                                                <v-text-field v-model="form.phone" label="Phone Number"
                                                    variant="outlined" :error-messages="form.errors.phone"
                                                    prepend-inner-icon="mdi-phone-outline" class="mb-4"
                                                    placeholder="+1 (555) 123-4567" :rules="phoneRules"
                                                    @blur="validateField('phone')" />
                                            </v-col>
                                            <v-col cols="12" md="6">
                                                <v-textarea v-model="form.address" label="Address" variant="outlined"
                                                    :error-messages="form.errors.address"
                                                    prepend-inner-icon="mdi-map-marker-outline" rows="2" class="mb-4"
                                                    placeholder="Enter your full address" :rules="addressRules"
                                                    @blur="validateField('address')" />
                                            </v-col>
                                        </v-row>

                                        <div class="text-center">
                                            <v-btn color="primary" size="large" @click="nextStep"
                                                :disabled="!isStep1Valid" class="px-8">
                                                Continue
                                                <v-icon right>mdi-arrow-right</v-icon>
                                            </v-btn>
                                        </div>
                                    </div>

                                    <!-- Step 2: Account Type & Security -->
                                    <div v-show="currentStep === 2" class="form-step">
                                        <h3 class="text-h5 font-weight-bold mb-6 text-center">
                                            Account Security & Type
                                        </h3>

                                        <!-- Password Fields -->
                                        <v-row>
                                            <v-col cols="12" md="6">
                                                <v-text-field v-model="form.password" label="Password"
                                                    :type="showPassword ? 'text' : 'password'" variant="outlined"
                                                    :error-messages="form.errors.password"
                                                    prepend-inner-icon="mdi-lock-outline"
                                                    :append-inner-icon="showPassword ? 'mdi-eye' : 'mdi-eye-off'"
                                                    @click:append-inner="showPassword = !showPassword" class="mb-4"
                                                    required :rules="passwordRules" @blur="validateField('password')" />
                                                <div class="password-strength mb-4">
                                                    <div class="text-caption text-grey-darken-1 mb-2">Password Strength:
                                                    </div>
                                                    <v-progress-linear :model-value="passwordStrength"
                                                        :color="passwordStrengthColor" height="4" rounded />
                                                </div>
                                            </v-col>
                                            <v-col cols="12" md="6">
                                                <v-text-field v-model="form.password_confirmation"
                                                    label="Confirm Password"
                                                    :type="showPasswordConfirm ? 'text' : 'password'" variant="outlined"
                                                    :error-messages="form.errors.password_confirmation"
                                                    prepend-inner-icon="mdi-lock-check-outline"
                                                    :append-inner-icon="showPasswordConfirm ? 'mdi-eye' : 'mdi-eye-off'"
                                                    @click:append-inner="showPasswordConfirm = !showPasswordConfirm"
                                                    class="mb-4" required :rules="confirmPasswordRules"
                                                    @blur="validateField('password_confirmation')" />
                                            </v-col>
                                        </v-row>

                                        <!-- Role Selection -->
                                        <div class="mb-6">
                                            <h4 class="text-h6 font-weight-bold mb-4 text-center">
                                                Choose Your Account Type
                                            </h4>
                                            <v-row>
                                                <v-col v-for="role in roleOptions" :key="role.value" cols="12" sm="6"
                                                    md="3">
                                                    <v-card :class="[
                                                        'role-card',
                                                        { 'role-card-selected': form.role === role.value }
                                                    ]" @click="selectRole(role.value)" elevation="2" hover>
                                                        <v-card-text class="text-center pa-4">
                                                            <v-icon :color="getRoleColor(role.value)" size="32"
                                                                class="mb-3">
                                                                {{ getRoleIcon(role.value) }}
                                                            </v-icon>
                                                            <h5 class="text-subtitle-1 font-weight-bold mb-2">
                                                                {{ role.title }}
                                                            </h5>
                                                            <p class="text-caption text-grey-darken-1">
                                                                {{ getRoleDescription(role.value) }}
                                                            </p>
                                                        </v-card-text>
                                                    </v-card>
                                                </v-col>
                                            </v-row>
                                        </div>

                                        <div class="d-flex justify-space-between">
                                            <v-btn variant="outlined" size="large" @click="prevStep" class="px-8">
                                                <v-icon left>mdi-arrow-left</v-icon>
                                                Back
                                            </v-btn>
                                            <v-btn color="primary" size="large" @click="nextStep"
                                                :disabled="!isStep2Valid" class="px-8">
                                                Continue
                                                <v-icon right>mdi-arrow-right</v-icon>
                                            </v-btn>
                                        </div>
                                    </div>

                                    <!-- Step 3: Review & Submit -->
                                    <div v-show="currentStep === 3" class="form-step">
                                        <h3 class="text-h5 font-weight-bold mb-6 text-center">
                                            Review Your Information
                                        </h3>

                                        <v-card variant="outlined" class="mb-6">
                                            <v-card-text>
                                                <v-row>
                                                    <v-col cols="12" md="6">
                                                        <div class="info-item mb-3">
                                                            <v-icon color="primary" class="me-2">mdi-account</v-icon>
                                                            <strong>Name:</strong> {{ form.name }}
                                                        </div>
                                                        <div class="info-item mb-3">
                                                            <v-icon color="primary" class="me-2">mdi-email</v-icon>
                                                            <strong>Email:</strong> {{ form.email }}
                                                        </div>
                                                        <div class="info-item mb-3">
                                                            <v-icon color="primary" class="me-2">mdi-phone</v-icon>
                                                            <strong>Phone:</strong> {{ form.phone || 'Not provided' }}
                                                        </div>
                                                    </v-col>
                                                    <v-col cols="12" md="6">
                                                        <div class="info-item mb-3">
                                                            <v-icon color="primary" class="me-2">mdi-map-marker</v-icon>
                                                            <strong>Address:</strong> {{ form.address || 'Not provided'
                                                            }}
                                                        </div>
                                                        <div class="info-item mb-3">
                                                            <v-icon :color="getRoleColor(form.role)" class="me-2">
                                                                {{ getRoleIcon(form.role) }}
                                                            </v-icon>
                                                            <strong>Account Type:</strong> {{ getRoleTitle(form.role) }}
                                                        </div>
                                                    </v-col>
                                                </v-row>
                                            </v-card-text>
                                        </v-card>

                                        <div class="d-flex justify-space-between">
                                            <v-btn variant="outlined" size="large" @click="prevStep" class="px-8">
                                                <v-icon left>mdi-arrow-left</v-icon>
                                                Back
                                            </v-btn>
                                            <v-btn type="submit" color="success" size="large" :loading="form.processing"
                                                class="px-8">
                                                <v-icon left>mdi-check-circle</v-icon>
                                                Create Account
                                            </v-btn>
                                        </div>
                                    </div>
                                </v-form>

                                <!-- Footer -->
                                <v-divider class="my-8" />
                                <div class="text-center">
                                    <span class="text-grey-darken-1">
                                        Already have an account?
                                    </span>
                                    <Link href="/login"
                                        class="text-primary text-decoration-none ml-1 font-weight-medium">
                                    Sign in here
                                    </Link>
                                </div>
                            </v-card>
                        </v-col>
                    </v-row>
                </v-container>
            </v-main>
        </div>
    </v-app>
</template>

<script setup>
    import { ref, computed, watch } from 'vue';
    import { useForm, Link } from '@inertiajs/vue3';

    // Reactive data
    const showPassword = ref(false);
    const showPasswordConfirm = ref(false);
    const currentStep = ref(1);
    const formRef = ref(null);
    const hasErrors = ref(false);

    // Form data
    const form = useForm({
        name: '',
        email: '',
        phone: '',
        address: '',
        password: '',
        password_confirmation: '',
        role: 'user',
        });

    // Role options
    const roleOptions = [
        { title: 'User', value: 'user' },
        { title: 'Administrator', value: 'admin' },
    ];

    // Validation rules
    const nameRules = [
        v => !!v || 'Name is required',
        v => (v && v.length >= 2) || 'Name must be at least 2 characters',
        v => (v && v.length <= 50) || 'Name must be less than 50 characters'
    ];

    const emailRules = [
        v => !!v || 'Email is required',
        v => /.+@.+\..+/.test(v) || 'Email must be valid'
    ];

    const phoneRules = [
        v => !v || /^[\+]?[1-9][\d]{0,15}$/.test(v.replace(/[\s\-\(\)]/g, '')) || 'Phone number must be valid'
    ];

    const addressRules = [
        v => !v || v.length <= 200 || 'Address must be less than 200 characters'
    ];

    const passwordRules = [
        v => !!v || 'Password is required',
        v => (v && v.length >= 8) || 'Password must be at least 8 characters'
    ];

    const confirmPasswordRules = [
        v => !!v || 'Password confirmation is required',
        v => v === form.password || 'Passwords do not match'
    ];

    // Computed properties
    const passwordStrength = computed(() => {
        if (!form.password) return 0;

        let strength = 0;
        if (form.password.length >= 8) strength += 25;
        if (form.password.length >= 12) strength += 25;
        if (/[A-Z]/.test(form.password)) strength += 25;
        if (/[a-z]/.test(form.password)) strength += 25;
        if (/\d/.test(form.password)) strength += 25;
        if (/[^A-Za-z0-9]/.test(form.password)) strength += 25;

        return Math.min(strength, 100);
    });

    const passwordStrengthColor = computed(() => {
        const strength = passwordStrength.value;
        if (strength < 40) return 'error';
        if (strength < 80) return 'warning';
        return 'success';
    });

    const isStep1Valid = computed(() => {
        return form.name && form.email &&
            nameRules.every(rule => rule(form.name) === true) &&
            emailRules.every(rule => rule(form.email) === true);
    });

    const isStep2Valid = computed(() => {
        return form.password && form.password_confirmation && form.role &&
            passwordRules.every(rule => rule(form.password) === true) &&
            confirmPasswordRules.every(rule => rule(form.password_confirmation) === true);
    });

    // Methods
    const getRoleColor = (role) => {
        const colors = {
            user: 'primary',
            admin: 'success'
        };
        return colors[role] || 'grey';
    };

    const getRoleIcon = (role) => {
        const icons = {
            user: 'mdi-account',
            admin: 'mdi-shield-account'
        };
        return icons[role] || 'mdi-account';
    };

    const getRoleDescription = (role) => {
        const descriptions = {
            user: 'Order food and manage your orders',
            admin: 'Manage restaurant operations and inventory'
        };
        return descriptions[role] || '';
    };

    const getRoleTitle = (role) => {
        const roleOption = roleOptions.find(r => r.value === role);
        return roleOption ? roleOption.title : role;
    };

    const selectRole = (role) => {
        form.role = role;
    };

    const validateField = (fieldName) => {
        // Trigger validation for specific field
        if (formRef.value) {
            formRef.value.validate();
        }
    };

    const nextStep = () => {
        if (currentStep.value < 3) {
            currentStep.value++;
        }
    };

    const prevStep = () => {
        if (currentStep.value > 1) {
            currentStep.value--;
        }
    };

    const submit = () => {
        form.post('/register', {
            onFinish: () => {
                form.reset('password', 'password_confirmation');
                currentStep.value = 1;
            },
            onError: () => {
                hasErrors.value = true;
                setTimeout(() => {
                    hasErrors.value = false;
                }, 1000);
            }
        });
    };

    // Watch for form errors
    watch(() => form.errors, () => {
        if (Object.keys(form.errors).length > 0) {
            hasErrors.value = true;
            setTimeout(() => {
                hasErrors.value = false;
            }, 1000);
        }
    }, { deep: true });
</script>

<style scoped>
.auth-background {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    min-height: 100vh;
    position: relative;
}

.auth-background::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grain" width="100" height="100" patternUnits="userSpaceOnUse"><circle cx="50" cy="50" r="1" fill="white" opacity="0.1"/></pattern></defs><rect width="100" height="100" fill="url(%23grain)"/></svg>');
    opacity: 0.3;
}

.min-height-screen {
    min-height: 100vh;
}

.auth-card {
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(10px);
    border-radius: 20px;
    border: 1px solid rgba(255, 255, 255, 0.2);
    transition: all 0.3s ease;
}

.auth-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
}

.auth-icon-wrapper {
    display: inline-block;
    padding: 20px;
    background: linear-gradient(135deg, #1976D2, #42A5F5);
    border-radius: 50%;
    box-shadow: 0 8px 20px rgba(25, 118, 210, 0.3);
}

.auth-icon {
    animation: pulse 2s infinite;
}

@keyframes pulse {
    0% {
        transform: scale(1);
    }

    50% {
        transform: scale(1.05);
    }

    100% {
        transform: scale(1);
    }
}

.stepper-custom {
    background: transparent !important;
}

.stepper-custom .v-stepper-header {
    box-shadow: none !important;
}

.form-step {
    animation: slideIn 0.3s ease-in-out;
}

@keyframes slideIn {
    from {
        opacity: 0;
        transform: translateX(20px);
    }

    to {
        opacity: 1;
        transform: translateX(0);
    }
}

.role-card {
    transition: all 0.3s ease;
    cursor: pointer;
    border: 2px solid transparent;
}

.role-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
}

.role-card-selected {
    border-color: #1976D2 !important;
    background: linear-gradient(135deg, rgba(25, 118, 210, 0.1), rgba(66, 165, 245, 0.1));
    transform: translateY(-5px);
    box-shadow: 0 8px 20px rgba(25, 118, 210, 0.2);
}

.password-strength {
    margin-top: 8px;
}

.info-item {
    display: flex;
    align-items: center;
    padding: 8px 0;
}

.shake {
    animation: shake 0.5s ease-in-out;
}

@keyframes shake {

    0%,
    100% {
        transform: translateX(0);
    }

    25% {
        transform: translateX(-5px);
    }

    75% {
        transform: translateX(5px);
    }
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .auth-card {
        margin: 20px;
        padding: 24px !important;
    }

    .role-card {
        margin-bottom: 16px;
    }
}

/* Custom scrollbar */
::-webkit-scrollbar {
    width: 8px;
}

::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 4px;
}

::-webkit-scrollbar-thumb {
    background: #1976D2;
    border-radius: 4px;
}

::-webkit-scrollbar-thumb:hover {
    background: #1565C0;
}
</style>
