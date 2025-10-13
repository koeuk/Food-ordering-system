<template>
  <DashboardLayout>
    <Head title="Admin Profile" />

    <v-container>
      <!-- Header -->
      <div class="d-flex justify-space-between align-center mb-6">
        <div>
          <h1 class="text-h3 font-weight-bold text-grey-darken-3 mb-2">
            Admin Profile
          </h1>
          <p class="text-grey-darken-1">
            Manage your account information and system preferences.
          </p>
        </div>
        <v-btn
          color="grey"
          variant="outlined"
          href="/dashboard/admin"
        >
          <v-icon left>mdi-arrow-left</v-icon>
          Back to Dashboard
        </v-btn>
      </div>

      <v-row>
        <!-- Profile Information -->
        <v-col cols="12" lg="8">
          <v-card elevation="2" class="mb-6">
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
                      label="Full Name"
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
                      label="Email Address"
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
                      label="Phone Number"
                      variant="outlined"
                      :error-messages="form.errors.phone"
                    />
                  </v-col>

                  <!-- Role -->
                  <v-col cols="12" md="6">
                    <v-text-field
                      :value="user.role"
                      label="Role"
                      variant="outlined"
                      readonly
                      disabled
                      color="success"
                    />
                  </v-col>

                  <!-- Address -->
                  <v-col cols="12">
                    <v-textarea
                      v-model="form.address"
                      label="Address"
                      variant="outlined"
                      rows="3"
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
                  <template v-slot:prepend>
                    <v-icon>mdi-alert</v-icon>
                  </template>
                  Your email address is unverified. Please check your email for verification instructions.
                </v-alert>

                <!-- Success Message -->
                <v-alert
                  v-if="status"
                  type="success"
                  variant="tonal"
                  class="mb-4"
                >
                  <template v-slot:prepend>
                    <v-icon>mdi-check-circle</v-icon>
                  </template>
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
                <v-icon left>mdi-content-save</v-icon>
                Save Changes
              </v-btn>
            </v-card-actions>
          </v-card>
        </v-col>

        <!-- Admin Stats & Actions -->
        <v-col cols="12" lg="4">
          <!-- Account Status -->
          <v-card elevation="2" class="mb-4">
            <v-card-title class="text-h6 font-weight-bold text-grey-darken-3">
              <v-icon left color="info">mdi-account-circle</v-icon>
              Account Status
            </v-card-title>
            <v-card-text>
              <v-list class="py-0">
                <v-list-item class="px-0">
                  <template v-slot:prepend>
                    <v-icon color="success">mdi-shield-check</v-icon>
                  </template>
                  <v-list-item-title>Admin Access</v-list-item-title>
                  <template v-slot:append>
                    <v-chip color="success" size="small">Active</v-chip>
                  </template>
                </v-list-item>
                
                <v-list-item class="px-0">
                  <template v-slot:prepend>
                    <v-icon :color="user.email_verified_at ? 'success' : 'warning'">
                      {{ user.email_verified_at ? 'mdi-email-check' : 'mdi-email-alert' }}
                    </v-icon>
                  </template>
                  <v-list-item-title>Email Status</v-list-item-title>
                  <template v-slot:append>
                    <v-chip 
                      :color="user.email_verified_at ? 'success' : 'warning'" 
                      size="small"
                    >
                      {{ user.email_verified_at ? 'Verified' : 'Unverified' }}
                    </v-chip>
                  </template>
                </v-list-item>

                <v-list-item class="px-0">
                  <template v-slot:prepend>
                    <v-icon color="info">mdi-calendar</v-icon>
                  </template>
                  <v-list-item-title>Member Since</v-list-item-title>
                  <template v-slot:append>
                    <span class="text-caption">{{ formatDate(user.created_at) }}</span>
                  </template>
                </v-list-item>
              </v-list>
            </v-card-text>
          </v-card>

          <!-- Quick Actions -->
          <v-card elevation="2" class="mb-4">
            <v-card-title class="text-h6 font-weight-bold text-grey-darken-3">
              <v-icon left color="primary">mdi-lightning-bolt</v-icon>
              Quick Actions
            </v-card-title>
            <v-card-text>
              <v-btn
                color="primary"
                variant="outlined"
                block
                class="mb-2"
                href="/dashboard/admin"
              >
                <v-icon left>mdi-view-dashboard</v-icon>
                Go to Dashboard
              </v-btn>
              
              <v-btn
                color="info"
                variant="outlined"
                block
                class="mb-2"
                href="/dashboard/users"
              >
                <v-icon left>mdi-account-group</v-icon>
                Manage Users
              </v-btn>
              
              <v-btn
                color="success"
                variant="outlined"
                block
                href="/dashboard/reports"
              >
                <v-icon left>mdi-chart-line</v-icon>
                View Reports
              </v-btn>
            </v-card-text>
          </v-card>

          <!-- Security -->
          <v-card elevation="2" color="error" variant="outlined">
            <v-card-title class="text-h6 font-weight-bold text-error">
              <v-icon left color="error">mdi-shield-alert</v-icon>
              Security
            </v-card-title>
            <v-card-text>
              <p class="text-body-2 mb-4">
                Permanently delete your admin account and all associated data.
              </p>
            </v-card-text>
            <v-card-actions>
              <v-btn
                color="error"
                variant="outlined"
                size="small"
                @click="confirmUserDeletion = true"
              >
                <v-icon left>mdi-delete</v-icon>
                Delete Account
              </v-btn>
            </v-card-actions>
          </v-card>
        </v-col>
      </v-row>

      <!-- Delete Account Confirmation Dialog -->
      <v-dialog v-model="confirmUserDeletion" max-width="500">
        <v-card>
          <v-card-title class="text-h6">
            <v-icon left color="error">mdi-alert</v-icon>
            Delete Admin Account
          </v-card-title>
          <v-card-text>
            <p class="mb-3">
              Are you sure you want to delete your admin account? This action cannot be undone.
            </p>
            <p class="text-body-2 text-error">
              <strong>Warning:</strong> This will permanently delete your account and remove your admin access to the system.
            </p>
            <p class="mt-3">
              Please enter your password to confirm you would like to permanently delete your account.
            </p>
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
              <v-icon left>mdi-delete</v-icon>
              Delete Account
            </v-btn>
          </v-card-actions>
        </v-card>
      </v-dialog>
    </v-container>
  </DashboardLayout>
</template>

<script setup>
import { ref, computed } from 'vue';
import { Head, useForm } from '@inertiajs/vue3';
import DashboardLayout from '@/Layouts/DashboardLayout.vue';

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
const valid = ref(false);
const confirmUserDeletion = ref(false);

const form = useForm({
  name: props.user.name,
  email: props.user.email,
  phone: props.user.phone || '',
  address: props.user.address || '',
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
};

const formatDate = (dateString) => {
  if (!dateString) return 'N/A';
  try {
    return new Date(dateString).toLocaleDateString('en-US', {
      month: 'short',
      day: 'numeric',
      year: 'numeric'
    });
  } catch (error) {
    return 'Invalid Date';
  }
};

const submit = () => {
  if (valid.value) {
    form.patch(route('profile.update'));
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
</script>

<style scoped>
.v-card {
  border-radius: 12px;
}

.v-btn {
  border-radius: 8px;
}
</style>
