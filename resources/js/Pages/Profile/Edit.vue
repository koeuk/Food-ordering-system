<template>
  <AppLayout>
    <Head title="Profile" />

    <v-container>
      <!-- Header -->
      <div class="d-flex justify-space-between align-center mb-6">
        <div>
          <h1 class="text-h3 font-weight-bold text-grey-darken-3 mb-2">
            Profile
          </h1>
          <p class="text-grey-darken-1">
            Update your account's profile information and email address.
          </p>
        </div>
      </div>

      <!-- Profile Form -->
      <v-card elevation="2">
        <v-card-title class="text-h6 font-weight-bold text-grey-darken-3">
          <v-icon left color="primary">mdi-account</v-icon>
          Profile Information
        </v-card-title>
        <v-card-text>
          <v-form ref="formRef" v-model="valid">
            <v-row>
              <!-- Name -->
              <v-col cols="12" md="6">
                <v-text-field
                  v-model="form.name"
                  label="Name"
                  variant="outlined"
                  :rules="[rules.required]"
                  :error-messages="form.errors.name"
                  required
                />
              </v-col>

              <!-- Email -->
              <v-col cols="12" md="6">
                <v-text-field
                  v-model="form.email"
                  label="Email"
                  type="email"
                  variant="outlined"
                  :rules="[rules.required, rules.email]"
                  :error-messages="form.errors.email"
                  required
                />
              </v-col>

              <!-- Phone -->
              <v-col cols="12" md="6">
                <v-text-field
                  v-model="form.phone"
                  label="Phone"
                  variant="outlined"
                  :error-messages="form.errors.phone"
                />
              </v-col>

              <!-- Address -->
              <v-col cols="12" md="6">
                <v-text-field
                  v-model="form.address"
                  label="Address"
                  variant="outlined"
                  :error-messages="form.errors.address"
                />
              </v-col>
            </v-row>

            <!-- Email Verification Status -->
            <v-alert
              v-if="mustVerifyEmail && user.email_verified_at === null"
              type="warning"
              variant="tonal"
              class="mb-4"
            >
              Your email address is unverified.
              <Link
                :href="route('verification.send')"
                method="post"
                as="button"
                class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 ml-1"
              >
                Click here to re-send the verification email.
              </Link>
            </v-alert>

            <!-- Success Message -->
            <v-alert
              v-if="status"
              type="success"
              variant="tonal"
              class="mb-4"
            >
              {{ status }}
            </v-alert>
          </v-form>
        </v-card-text>
        <v-card-actions>
          <v-spacer />
          <v-btn
            color="primary"
            size="large"
            :disabled="!valid"
            :loading="form.processing"
            @click="submit"
          >
            Save
          </v-btn>
        </v-card-actions>
      </v-card>

      <!-- Update Password Section -->
      <v-card elevation="2" class="mt-6">
        <v-card-title class="text-h6 font-weight-bold text-grey-darken-3">
          <v-icon left color="primary">mdi-lock</v-icon>
          Update Password
        </v-card-title>
        <v-card-text>
          <v-form ref="passwordFormRef" v-model="passwordFormValid">
            <v-row>
              <!-- Current Password -->
              <v-col cols="12">
                <v-text-field
                  v-model="passwordForm.current_password"
                  label="Current Password"
                  type="password"
                  variant="outlined"
                  :rules="[rules.required]"
                  :error-messages="passwordForm.errors.current_password"
                  required
                  prepend-inner-icon="mdi-lock-outline"
                />
              </v-col>

              <!-- New Password -->
              <v-col cols="12" md="6">
                <v-text-field
                  v-model="passwordForm.password"
                  label="New Password"
                  type="password"
                  variant="outlined"
                  :rules="[rules.required, rules.password]"
                  :error-messages="passwordForm.errors.password"
                  required
                  prepend-inner-icon="mdi-lock-plus"
                />
              </v-col>

              <!-- Confirm Password -->
              <v-col cols="12" md="6">
                <v-text-field
                  v-model="passwordForm.password_confirmation"
                  label="Confirm New Password"
                  type="password"
                  variant="outlined"
                  :rules="[rules.required, rules.passwordConfirmation]"
                  :error-messages="passwordForm.errors.password_confirmation"
                  required
                  prepend-inner-icon="mdi-lock-check"
                />
              </v-col>
            </v-row>

            <!-- Password Requirements -->
            <v-alert type="info" variant="tonal" class="mb-4">
              <template v-slot:prepend>
                <v-icon>mdi-information</v-icon>
              </template>
              <div>
                <div class="font-weight-bold mb-2">Password Requirements:</div>
                <ul class="text-body-2 mb-0">
                  <li>At least 8 characters long</li>
                  <li>Contains at least one uppercase letter</li>
                  <li>Contains at least one lowercase letter</li>
                  <li>Contains at least one number</li>
                  <li>Contains at least one special character</li>
                </ul>
              </div>
            </v-alert>

            <!-- Success Message -->
            <v-alert
              v-if="passwordStatus"
              type="success"
              variant="tonal"
              class="mb-4"
            >
              {{ passwordStatus }}
            </v-alert>
          </v-form>
        </v-card-text>
        <v-card-actions>
          <v-spacer />
          <v-btn
            color="primary"
            size="large"
            :disabled="!passwordFormValid"
            :loading="passwordForm.processing"
            @click="updatePassword"
          >
            <v-icon left>mdi-lock-reset</v-icon>
            Update Password
          </v-btn>
        </v-card-actions>
      </v-card>

      <!-- Delete Account Section -->
      <v-card elevation="2" class="mt-6" color="error" variant="outlined">
        <v-card-title class="text-h6 font-weight-bold text-error">
          <v-icon left color="error">mdi-delete-alert</v-icon>
          Delete Account
        </v-card-title>
        <v-card-text>
          <p class="text-body-1 mb-4">
            Once your account is deleted, all of its resources and data will be permanently deleted. 
            Before deleting your account, please download any data or information that you wish to retain.
          </p>
        </v-card-text>
        <v-card-actions>
          <v-spacer />
          <v-btn
            color="error"
            variant="outlined"
            @click="confirmUserDeletion = true"
          >
            Delete Account
          </v-btn>
        </v-card-actions>
      </v-card>

      <!-- Delete Account Confirmation Dialog -->
      <v-dialog v-model="confirmUserDeletion" max-width="500">
        <v-card>
          <v-card-title class="text-h6">
            Are you sure you want to delete your account?
          </v-card-title>
          <v-card-text>
            Once your account is deleted, all of its resources and data will be permanently deleted. 
            Please enter your password to confirm you would like to permanently delete your account.
          </v-card-text>
          <v-card-text>
            <v-text-field
              v-model="deleteForm.password"
              label="Password"
              type="password"
              variant="outlined"
              :error-messages="deleteForm.errors.password"
              required
            />
          </v-card-text>
          <v-card-actions>
            <v-spacer />
            <v-btn
              color="grey"
              variant="outlined"
              @click="confirmUserDeletion = false"
            >
              Cancel
            </v-btn>
            <v-btn
              color="error"
              :loading="deleteForm.processing"
              @click="deleteUser"
            >
              Delete Account
            </v-btn>
          </v-card-actions>
        </v-card>
      </v-dialog>
    </v-container>
  </AppLayout>
</template>

<script setup>
import { ref, computed } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

const props = defineProps({
  mustVerifyEmail: {
    type: Boolean,
    required: true,
  },
  status: {
    type: String,
    default: null,
  },
  user: {
    type: Object,
    required: true,
  },
});

const formRef = ref(null);
const passwordFormRef = ref(null);
const valid = ref(false);
const passwordFormValid = ref(false);
const confirmUserDeletion = ref(false);

const form = useForm({
  name: props.user.name,
  email: props.user.email,
  phone: props.user.phone || '',
  address: props.user.address || '',
});

const passwordForm = useForm({
  current_password: '',
  password: '',
  password_confirmation: '',
});

const deleteForm = useForm({
  password: '',
});

const rules = {
  required: (value) => !!value || 'This field is required',
  email: (value) => {
    const pattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return pattern.test(value) || 'Enter a valid email address';
  },
  password: (value) => {
    if (!value) return 'Password is required';
    if (value.length < 8) return 'Password must be at least 8 characters';
    if (!/(?=.*[a-z])/.test(value)) return 'Password must contain at least one lowercase letter';
    if (!/(?=.*[A-Z])/.test(value)) return 'Password must contain at least one uppercase letter';
    if (!/(?=.*\d)/.test(value)) return 'Password must contain at least one number';
    if (!/(?=.*[@$!%*?&])/.test(value)) return 'Password must contain at least one special character';
    return true;
  },
  passwordConfirmation: (value) => {
    return value === passwordForm.password || 'Passwords do not match';
  },
};

const submit = () => {
  if (valid.value) {
    form.patch(route('profile.update'));
  }
};

const updatePassword = () => {
  if (passwordFormValid.value) {
    passwordForm.put(route('password.update'), {
      preserveScroll: true,
      onSuccess: () => {
        passwordForm.reset();
        passwordFormRef.value?.reset();
      },
      onError: () => {
        if (passwordForm.errors.current_password) {
          passwordForm.reset('current_password');
        }
        if (passwordForm.errors.password) {
          passwordForm.reset('password', 'password_confirmation');
        }
      },
      onFinish: () => passwordForm.reset(),
    });
  }
};

const deleteUser = () => {
  deleteForm.delete(route('profile.destroy'), {
    preserveScroll: true,
    onSuccess: () => closeModal(),
    onError: () => {
      if (deleteForm.errors.password) {
        deleteForm.reset('password');
      }
    },
    onFinish: () => deleteForm.reset(),
  });
};

const closeModal = () => {
  confirmUserDeletion.value = false;
  deleteForm.reset();
};

// Computed property for password status
const passwordStatus = computed(() => {
  // This will be set by the backend when password update is successful
  return props.status && props.status.includes('password') ? props.status : null;
});
</script>
