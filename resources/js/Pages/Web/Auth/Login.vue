<template>
  <v-app>
    <v-main>
      <v-container fluid class="fill-height">
        <v-row align="center" justify="center">
          <v-col cols="12" sm="8" md="4">
            <v-card elevation="8" class="pa-4">
              <v-card-title class="text-center">
                <h1 class="text-h4 font-weight-bold text-primary">User Login</h1>
                <p class="text-grey-darken-1 mt-2">Welcome back! Sign in to your account</p>
              </v-card-title>
              
              <v-card-text>
                <v-form @submit.prevent="submit">
                  <v-text-field
                    v-model="form.email"
                    label="Email"
                    type="email"
                    variant="outlined"
                    :error-messages="form.errors.email"
                    required
                    autofocus
                    autocomplete="username"
                  />

                  <v-text-field
                    v-model="form.password"
                    label="Password"
                    type="password"
                    variant="outlined"
                    :error-messages="form.errors.password"
                    required
                    autocomplete="current-password"
                  />

                  <v-checkbox
                    v-model="form.remember"
                    label="Remember me"
                    color="primary"
                  />

                  <v-btn
                    type="submit"
                    color="primary"
                    size="large"
                    block
                    class="mt-4"
                    :disabled="form.processing"
                  >
                    <v-icon left>mdi-login</v-icon>
                    {{ form.processing ? 'Logging in...' : 'Login' }}
                  </v-btn>
                </v-form>

                <!-- Demo Accounts -->
                <v-divider class="my-4"></v-divider>
                <div class="text-center">
                  <p class="text-body-2 text-grey-darken-1 mb-2">Demo Accounts:</p>
                  <div class="d-flex justify-center gap-2">
                    <v-btn size="small" color="blue" variant="outlined" @click="fillDemoCredentials('user')">
                      User Demo
                    </v-btn>
                    <v-btn size="small" color="blue" variant="outlined" @click="fillDemoCredentials('admin')" class="ml-2">
                      Admin Demo
                    </v-btn>
                  </div>
                  <p class="text-caption text-grey mt-2">
                    <strong>User:</strong> user@test.com / password<br>
                    <strong>Admin:</strong> admin@test.com / password
                  </p>
                </div>
              </v-card-text>

              <v-card-actions class="flex-column">
                <v-btn
                  variant="text"
                  color="primary"
                  href="/login"
                  class="mb-2"
                >
                  <v-icon left>mdi-shield-account</v-icon>
                  Admin Login
                </v-btn>
                
                <v-btn
                  variant="text"
                  color="grey"
                  href="/register"
                >
                  Don't have an account? Register
                </v-btn>
              </v-card-actions>
            </v-card>
          </v-col>
        </v-row>
      </v-container>
    </v-main>
  </v-app>
</template>

<script setup>
import { useForm } from '@inertiajs/vue3';

const form = useForm({
  email: '',
  password: '',
  remember: false,
});

const fillDemoCredentials = (role) => {
  const credentials = {
    user: { email: 'user@test.com', password: 'password' },
    admin: { email: 'admin@test.com', password: 'password' },
  };
  const creds = credentials[role];
  form.email = creds.email;
  form.password = creds.password;
};

const submit = () => {
  console.log('Login form submitted');
  console.log('Form data:', {
    email: form.email,
    password: form.password ? '***' : '',
    remember: form.remember
  });
  console.log('Route:', route('login'));
  
  form.post(route('login'), {
    onStart: () => {
      console.log('Login request started');
    },
    onSuccess: (page) => {
      console.log('Login successful', page);
    },
    onError: (errors) => {
      console.error('Login error:', errors);
    },
    onFinish: () => {
      console.log('Login request finished');
      form.reset('password');
    },
  });
};
</script>
