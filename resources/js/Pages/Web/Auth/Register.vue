<template>
  <v-app>
    <v-main>
      <v-container fluid class="fill-height">
        <v-row align="center" justify="center">
          <v-col cols="12" sm="8" md="6">
            <v-card elevation="8" class="pa-4">
              <v-card-title class="text-center">
                <h1 class="text-h4 font-weight-bold text-primary">User Registration</h1>
                <p class="text-grey-darken-1 mt-2">Create your account to start ordering</p>
              </v-card-title>
              
              <v-card-text>
                <v-form @submit.prevent="submit">
                  <v-row>
                    <v-col cols="12" md="6">
                      <v-text-field
                        v-model="form.name"
                        label="Full Name"
                        variant="outlined"
                        :error-messages="form.errors.name"
                        required
                        autofocus
                        autocomplete="name"
                      />
                    </v-col>
                    <v-col cols="12" md="6">
                      <v-text-field
                        v-model="form.email"
                        label="Email"
                        type="email"
                        variant="outlined"
                        :error-messages="form.errors.email"
                        required
                        autocomplete="username"
                      />
                    </v-col>
                  </v-row>

                  <v-row>
                    <v-col cols="12" md="6">
                      <v-text-field
                        v-model="form.password"
                        label="Password"
                        type="password"
                        variant="outlined"
                        :error-messages="form.errors.password"
                        required
                        autocomplete="new-password"
                      />
                    </v-col>
                    <v-col cols="12" md="6">
                      <v-text-field
                        v-model="form.password_confirmation"
                        label="Confirm Password"
                        type="password"
                        variant="outlined"
                        :error-messages="form.errors.password_confirmation"
                        required
                        autocomplete="new-password"
                      />
                    </v-col>
                  </v-row>

                  <v-row>
                    <v-col cols="12" md="6">
                      <v-text-field
                        v-model="form.phone"
                        label="Phone Number"
                        variant="outlined"
                        :error-messages="form.errors.phone"
                        autocomplete="tel"
                      />
                    </v-col>
                    <v-col cols="12" md="6">
                      <v-text-field
                        v-model="form.address"
                        label="Address"
                        variant="outlined"
                        :error-messages="form.errors.address"
                        autocomplete="street-address"
                      />
                    </v-col>
                  </v-row>

                  <v-select
                    v-model="form.role"
                    :items="roleOptions"
                    label="Account Type"
                    variant="outlined"
                    :error-messages="form.errors.role"
                    required
                  >
                    <template v-slot:item="{ props, item }">
                      <v-list-item v-bind="props">
                        <template v-slot:prepend>
                          <v-icon :color="getRoleColor(item.value)">{{ getRoleIcon(item.value) }}</v-icon>
                        </template>
                        <v-list-item-title>{{ item.title }}</v-list-item-title>
                        <v-list-item-subtitle>{{ getRoleDescription(item.value) }}</v-list-item-subtitle>
                      </v-list-item>
                    </template>
                  </v-select>

                  <v-checkbox
                    v-model="form.terms"
                    color="primary"
                    :error-messages="form.errors.terms"
                  >
                    <template v-slot:label>
                      <span class="text-body-2">
                        I agree to the 
                        <a href="#" class="text-primary">Terms of Service</a> 
                        and 
                        <a href="#" class="text-primary">Privacy Policy</a>
                      </span>
                    </template>
                  </v-checkbox>

                  <v-btn
                    type="submit"
                    color="primary"
                    size="large"
                    block
                    class="mt-4"
                    :disabled="form.processing"
                  >
                    <v-icon left>mdi-account-plus</v-icon>
                    {{ form.processing ? 'Creating Account...' : 'Create Account' }}
                  </v-btn>
                </v-form>
              </v-card-text>

              <v-card-actions class="flex-column">
                <v-btn
                  variant="text"
                  color="primary"
                  href="/login"
                  class="mb-2"
                >
                  <v-icon left>mdi-login</v-icon>
                  Already have an account? Login
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
  name: '',
  email: '',
  password: '',
  password_confirmation: '',
  phone: '',
  address: '',
  role: 'user',
  terms: false,
});

const roleOptions = [
  { title: 'User', value: 'user' },
  { title: 'Administrator', value: 'admin' },
];

const getRoleColor = (role) => {
  const colors = { user: 'primary', admin: 'success' };
  return colors[role] || 'grey';
};

const getRoleIcon = (role) => {
  const icons = { user: 'mdi-account', admin: 'mdi-shield-account' };
  return icons[role] || 'mdi-account';
};

const getRoleDescription = (role) => {
  const descriptions = { 
    user: 'Order food and manage your orders', 
    admin: 'Manage restaurant operations and inventory' 
  };
  return descriptions[role] || '';
};

const submit = () => {
  form.post(route('register'), {
    onFinish: () => form.reset('password', 'password_confirmation'),
  });
};
</script>
