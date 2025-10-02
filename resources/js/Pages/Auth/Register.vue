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
                  Create Account
                </h1>
                <p class="text-grey-darken-1">
                  Join our food ordering system
                </p>
              </div>

              <!-- Registration Form -->
              <v-form @submit.prevent="submit">
                <v-row>
                  <v-col cols="12" sm="6">
                    <v-text-field
                      v-model="form.first_name"
                      label="First Name"
                      variant="outlined"
                      :error-messages="form.errors.first_name"
                      prepend-inner-icon="mdi-account"
                      required
                    />
                  </v-col>
                  <v-col cols="12" sm="6">
                    <v-text-field
                      v-model="form.last_name"
                      label="Last Name"
                      variant="outlined"
                      :error-messages="form.errors.last_name"
                      prepend-inner-icon="mdi-account"
                      required
                    />
                  </v-col>
                </v-row>

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
                  v-model="form.phone"
                  label="Phone Number"
                  variant="outlined"
                  :error-messages="form.errors.phone"
                  prepend-inner-icon="mdi-phone"
                  class="mb-4"
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

                <v-text-field
                  v-model="form.password_confirmation"
                  label="Confirm Password"
                  :type="showPasswordConfirm ? 'text' : 'password'"
                  variant="outlined"
                  :error-messages="form.errors.password_confirmation"
                  prepend-inner-icon="mdi-lock-check"
                  :append-inner-icon="showPasswordConfirm ? 'mdi-eye' : 'mdi-eye-off'"
                  @click:append-inner="showPasswordConfirm = !showPasswordConfirm"
                  class="mb-4"
                  required
                />

                <v-select
                  v-model="form.role"
                  :items="roleOptions"
                  label="Account Type"
                  variant="outlined"
                  :error-messages="form.errors.role"
                  prepend-inner-icon="mdi-account-group"
                  class="mb-4"
                  required
                />

                <v-checkbox
                  v-model="form.terms"
                  :error-messages="form.errors.terms"
                  class="mb-4"
                >
                  <template v-slot:label>
                    <span class="text-caption">
                      I agree to the 
                      <a href="#" class="text-primary text-decoration-none">Terms of Service</a>
                      and 
                      <a href="#" class="text-primary text-decoration-none">Privacy Policy</a>
                    </span>
                  </template>
                </v-checkbox>

                <v-btn
                  type="submit"
                  color="primary"
                  size="large"
                  block
                  :loading="form.processing"
                  class="mb-4"
                >
                  Create Account
                </v-btn>
              </v-form>

              <!-- Divider -->
              <v-divider class="my-6" />

              <!-- Login Link -->
              <div class="text-center">
                <span class="text-grey-darken-1">
                  Already have an account?
                </span>
                <router-link
                  to="/login"
                  class="text-primary text-decoration-none ml-1 font-weight-medium"
                >
                  Sign in
                </router-link>
              </div>
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
const showPasswordConfirm = ref(false);

const form = useForm({
  first_name: '',
  last_name: '',
  email: '',
  phone: '',
  password: '',
  password_confirmation: '',
  role: 'customer',
  terms: false,
});

const roleOptions = [
  { title: 'Customer', value: 'customer' },
  { title: 'Restaurant Manager', value: 'manager' },
  { title: 'Kitchen Staff', value: 'kitchen' },
];

const submit = () => {
  form.post('/register', {
    onFinish: () => form.reset('password', 'password_confirmation'),
  });
};
</script>
