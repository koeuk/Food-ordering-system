<template>
  <v-app :theme="isDark ? 'dark' : 'light'">
    <v-app-bar app color="primary" dark elevation="0">
      <v-spacer />
      <!-- Theme Toggle -->
      <v-btn
        icon
        variant="text"
        @click="toggleTheme"
        class="mr-2"
        :title="isDark ? 'Switch to Light Theme' : 'Switch to Dark Theme'"
      >
        <v-icon>{{ isDark ? 'mdi-weather-sunny' : 'mdi-weather-night' }}</v-icon>
      </v-btn>
    </v-app-bar>
    <v-main>
      <v-container fluid class="fill-height">
        <v-row align="center" justify="center">
          <v-col cols="12" sm="8" md="4">
            <v-card elevation="8" class="pa-4">
              <v-card-title class="text-center">
                <h1 class="text-h4 font-weight-bold text-primary">Admin Login</h1>
                <p class="text-grey-darken-1 mt-2">Dashboard Access</p>
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
              </v-card-text>

              <v-card-actions class="flex-column">
                <v-btn
                  variant="text"
                  color="primary"
                  :to="{ name: 'web.auth.login' }"
                  class="mb-2"
                >
                  <v-icon left>mdi-account</v-icon>
                  User Login
                </v-btn>
                
                <v-btn
                  variant="text"
                  color="grey"
                  :to="{ name: 'web.auth.register' }"
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
import { useTheme } from '@/composables/useTheme';

// Theme management
const { isDark, toggleTheme } = useTheme();

const form = useForm({
  email: '',
  password: '',
  remember: false,
});

const submit = () => {
  form.post(route('login'), {
    onFinish: () => form.reset('password'),
  });
};
</script>
