<template>
  <v-app>
    <v-main>
      <v-container fluid class="fill-height">
        <v-row align="center" justify="center">
          <v-col cols="12" sm="8" md="6" lg="4">
            <v-card elevation="8" class="pa-6">
              <!-- Header -->
              <div class="text-center mb-6">
                <h1 class="text-h4 font-weight-bold text-primary mb-2">
                  Food Ordering System
                </h1>
                <p class="text-grey-darken-1">
                  Sign in to your account
                </p>
              </div>

              <!-- Login Form -->
              <v-form @submit.prevent="submit">
                <v-text-field
                  v-model="form.email"
                  label="Email Address"
                  type="email"
                  variant="outlined"
                  :error-messages="form.errors.email"
                  prepend-inner-icon="mdi-email"
                  class="mb-4"
                  required
                />

                <v-text-field
                  v-model="form.password"
                  label="Password"
                  :type="showPassword ? 'text' : 'password'"
                  variant="outlined"
                  :error-messages="form.errors.password"
                  prepend-inner-icon="mdi-lock"
                  :append-inner-icon="showPassword ? 'mdi-eye' : 'mdi-eye-off'"
                  @click:append-inner="showPassword = !showPassword"
                  class="mb-4"
                  required
                />

                <div class="d-flex justify-space-between align-center mb-4">
                  <v-checkbox
                    v-model="form.remember"
                    label="Remember me"
                    hide-details
                  />
                  <a href="#" class="text-primary text-decoration-none">
                    Forgot password?
                  </a>
                </div>

                <v-btn
                  type="submit"
                  color="primary"
                  size="large"
                  block
                  :loading="form.processing"
                  class="mb-4"
                >
                  Sign In
                </v-btn>
              </v-form>

              <!-- Divider -->
              <v-divider class="my-6" />

              <!-- Register Link -->
              <div class="text-center">
                <span class="text-grey-darken-1">
                  Don't have an account?
                </span>
                <router-link
                  to="/register"
                  class="text-primary text-decoration-none ml-1 font-weight-medium"
                >
                  Sign up
                </router-link>
              </div>

              <!-- Demo Accounts -->
              <v-card
                v-if="!form.processing"
                variant="outlined"
                class="mt-6 pa-4"
                color="info"
              >
                <v-card-title class="text-subtitle-1 pa-0 mb-2">
                  <v-icon left color="info">mdi-information</v-icon>
                  Demo Accounts
                </v-card-title>
                <v-card-text class="pa-0">
                  <div class="text-caption mb-2">
                    <strong>Customer:</strong> customer@test.com / password<br>
                    <strong>Manager:</strong> manager@test.com / password<br>
                    <strong>Kitchen:</strong> kitchen@test.com / password
                  </div>
                  <v-btn
                    size="small"
                    color="info"
                    variant="outlined"
                    @click="fillDemoCredentials('customer')"
                  >
                    Customer Demo
                  </v-btn>
                  <v-btn
                    size="small"
                    color="info"
                    variant="outlined"
                    @click="fillDemoCredentials('manager')"
                    class="ml-2"
                  >
                    Manager Demo
                  </v-btn>
                </v-card-text>
              </v-card>
            </v-card>
          </v-col>
        </v-row>
      </v-container>
    </v-main>
  </v-app>
</template>

<script setup>
import { ref } from 'vue';
import { useForm } from '@inertiajs/vue3';

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
    customer: { email: 'customer@test.com', password: 'password' },
    manager: { email: 'manager@test.com', password: 'password' },
    kitchen: { email: 'kitchen@test.com', password: 'password' },
  };

  const creds = credentials[role];
  form.email = creds.email;
  form.password = creds.password;
};
</script>
