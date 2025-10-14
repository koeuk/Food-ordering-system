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

      <!-- Tabs Navigation -->
      <v-tabs v-model="activeTab" class="mb-6" color="primary">
        <v-tab value="profile">
          <v-icon start>mdi-account</v-icon>
          Profile Information
        </v-tab>
        <v-tab value="password">
          <v-icon start>mdi-lock</v-icon>
          Change Password
        </v-tab>
        <v-tab value="users">
          <v-icon start>mdi-account-group</v-icon>
          Manage Users
        </v-tab>
      </v-tabs>

      <v-tabs-window v-model="activeTab">
        <!-- Profile Information Tab -->
        <v-tabs-window-item value="profile">
          <v-row>
            <!-- Profile Information -->
            <v-col cols="12" lg="8">
          <v-card elevation="2" class="mb-6">
            <v-card-title class="text-h6 font-weight-bold text-grey-darken-3">
              <v-icon left color="primary">mdi-account</v-icon>
              Profile Information
            </v-card-title>
            <v-card-text>
              <!-- Profile Picture Upload -->
              <div class="text-center mb-6">
                <v-avatar size="120" class="mb-4">
                  <v-img
                    v-if="profileImage"
                    :src="profileImage"
                    alt="Profile Picture"
                  />
                  <v-icon v-else size="60" color="grey-lighten-1">mdi-account</v-icon>
                </v-avatar>
                <div>
                  <v-btn
                    color="primary"
                    variant="outlined"
                    size="small"
                    @click="$refs.profileImageInput.click()"
                  >
                    <v-icon left>mdi-camera</v-icon>
                    {{ profileImage ? 'Change Photo' : 'Upload Photo' }}
                  </v-btn>
                  <input
                    ref="profileImageInput"
                    type="file"
                    accept="image/*"
                    style="display: none"
                    @change="handleImageUpload"
                  />
                </div>
                <p class="text-caption text-grey-darken-1 mt-2">
                  JPG, PNG or GIF (Max 2MB)
                </p>
              </div>

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
        </v-tabs-window-item>

        <!-- Change Password Tab -->
        <v-tabs-window-item value="password">
          <v-row>
            <v-col cols="12" lg="8">
              <v-card elevation="2" class="mb-6">
                <v-card-title class="text-h6 font-weight-bold text-grey-darken-3">
                  <v-icon left color="warning">mdi-lock</v-icon>
                  Change Password
                </v-card-title>
                <v-card-text>
                  <v-form ref="passwordFormRef" v-model="passwordValid">
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
                        />
                      </v-col>

                      <!-- New Password -->
                      <v-col cols="12" md="6">
                        <v-text-field
                          v-model="passwordForm.password"
                          label="New Password"
                          type="password"
                          variant="outlined"
                          :rules="[rules.required, rules.minLength]"
                          :error-messages="passwordForm.errors.password"
                          required
                        />
                      </v-col>

                      <!-- Confirm Password -->
                      <v-col cols="12" md="6">
                        <v-text-field
                          v-model="passwordForm.password_confirmation"
                          label="Confirm New Password"
                          type="password"
                          variant="outlined"
                          :rules="[rules.required, rules.passwordMatch]"
                          :error-messages="passwordForm.errors.password_confirmation"
                          required
                        />
                      </v-col>
                    </v-row>

                    <!-- Password Requirements -->
                    <v-alert type="info" variant="tonal" class="mb-4">
                      <template v-slot:prepend>
                        <v-icon>mdi-information</v-icon>
                      </template>
                      <div class="text-body-2">
                        <strong>Password Requirements:</strong>
                        <ul class="mt-2 mb-0">
                          <li>At least 8 characters long</li>
                          <li>Contains uppercase and lowercase letters</li>
                          <li>Contains at least one number</li>
                          <li>Contains at least one special character</li>
                        </ul>
                      </div>
                    </v-alert>
                  </v-form>
                </v-card-text>
                <v-card-actions>
                  <v-spacer />
                  <v-btn
                    color="warning"
                    size="large"
                    :disabled="!passwordValid"
                    :loading="passwordForm.processing"
                    @click="updatePassword"
                  >
                    <v-icon left>mdi-lock-reset</v-icon>
                    Update Password
                  </v-btn>
                </v-card-actions>
              </v-card>
            </v-col>
          </v-row>
        </v-tabs-window-item>

        <!-- Manage Users Tab -->
        <v-tabs-window-item value="users">
          <v-row>
            <v-col cols="12">
              <v-card elevation="2">
                <v-card-title class="text-h6 font-weight-bold text-grey-darken-3">
                  <v-icon left color="info">mdi-account-group</v-icon>
                  User Management
                  <v-spacer />
                  <v-btn
                    color="primary"
                    size="small"
                    @click="refreshUsers"
                    :loading="loadingUsers"
                  >
                    <v-icon left>mdi-refresh</v-icon>
                    Refresh
                  </v-btn>
                </v-card-title>
                <v-card-text>
                  <!-- Users Statistics -->
                  <v-row class="mb-4">
                    <v-col cols="12" sm="6" md="3">
                      <v-card color="primary" variant="tonal" class="pa-4">
                        <div class="d-flex align-center">
                          <v-icon color="primary" class="mr-3">mdi-account-group</v-icon>
                          <div>
                            <div class="text-h6 font-weight-bold">{{ users.length }}</div>
                            <div class="text-caption">Total Users</div>
                          </div>
                        </div>
                      </v-card>
                    </v-col>
                    <v-col cols="12" sm="6" md="3">
                      <v-card color="success" variant="tonal" class="pa-4">
                        <div class="d-flex align-center">
                          <v-icon color="success" class="mr-3">mdi-account-check</v-icon>
                          <div>
                            <div class="text-h6 font-weight-bold">{{ activeUsers }}</div>
                            <div class="text-caption">Active Users</div>
                          </div>
                        </div>
                      </v-card>
                    </v-col>
                    <v-col cols="12" sm="6" md="3">
                      <v-card color="warning" variant="tonal" class="pa-4">
                        <div class="d-flex align-center">
                          <v-icon color="warning" class="mr-3">mdi-account-clock</v-icon>
                          <div>
                            <div class="text-h6 font-weight-bold">{{ recentUsers }}</div>
                            <div class="text-caption">Recent Users</div>
                          </div>
                        </div>
                      </v-card>
                    </v-col>
                    <v-col cols="12" sm="6" md="3">
                      <v-card color="info" variant="tonal" class="pa-4">
                        <div class="d-flex align-center">
                          <v-icon color="info" class="mr-3">mdi-account-star</v-icon>
                          <div>
                            <div class="text-h6 font-weight-bold">{{ adminUsers }}</div>
                            <div class="text-caption">Admin Users</div>
                          </div>
                        </div>
                      </v-card>
                    </v-col>
                  </v-row>

                  <!-- Users Table -->
                  <v-data-table
                    :headers="userHeaders"
                    :items="users"
                    :loading="loadingUsers"
                    class="elevation-1"
                  >
                    <!-- Avatar -->
                    <template #item.avatar="{ item }">
                      <v-avatar size="40">
                        <v-img
                          v-if="item.profile_image_url"
                          :src="item.profile_image_url"
                          :alt="item.name"
                        />
                        <v-icon v-else color="grey-lighten-1">mdi-account</v-icon>
                      </v-avatar>
                    </template>

                    <!-- Status -->
                    <template #item.status="{ item }">
                      <v-chip
                        :color="item.email_verified_at ? 'success' : 'warning'"
                        size="small"
                      >
                        {{ item.email_verified_at ? 'Verified' : 'Unverified' }}
                      </v-chip>
                    </template>

                    <!-- Role -->
                    <template #item.role="{ item }">
                      <v-chip
                        :color="item.role === 'admin' ? 'error' : 'primary'"
                        size="small"
                      >
                        {{ item.role === 'admin' ? 'Admin' : 'User' }}
                      </v-chip>
                    </template>

                    <!-- Last Login -->
                    <template #item.last_login="{ item }">
                      <span v-if="item.last_login_at">
                        {{ formatDate(item.last_login_at) }}
                      </span>
                      <span v-else class="text-grey">Never</span>
                    </template>

                    <!-- Actions -->
                    <template #item.actions="{ item }">
                      <v-menu>
                        <template v-slot:activator="{ props }">
                          <v-btn
                            icon
                            size="small"
                            color="grey"
                            v-bind="props"
                          >
                            <v-icon>mdi-dots-vertical</v-icon>
                          </v-btn>
                        </template>
                        <v-list density="compact">
                          <v-list-item @click="viewUser(item)">
                            <template v-slot:prepend>
                              <v-icon color="primary">mdi-eye</v-icon>
                            </template>
                            <v-list-item-title>View Details</v-list-item-title>
                          </v-list-item>
                          <v-list-item @click="editUser(item)">
                            <template v-slot:prepend>
                              <v-icon color="warning">mdi-pencil</v-icon>
                            </template>
                            <v-list-item-title>Edit User</v-list-item-title>
                          </v-list-item>
                          <v-divider v-if="item.id !== user.id" />
                          <v-list-item 
                            @click="confirmDeleteUser(item)"
                            v-if="item.id !== user.id"
                          >
                            <template v-slot:prepend>
                              <v-icon color="error">mdi-delete</v-icon>
                            </template>
                            <v-list-item-title>Delete User</v-list-item-title>
                          </v-list-item>
                        </v-list>
                      </v-menu>
                    </template>
                  </v-data-table>
                </v-card-text>
              </v-card>
            </v-col>
          </v-row>
        </v-tabs-window-item>
      </v-tabs-window>

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
import { ref, computed, onMounted } from 'vue';
import { Head, useForm, router } from '@inertiajs/vue3';
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
  users: {
    type: Array,
    default: () => []
  }
});

const formRef = ref(null);
const passwordFormRef = ref(null);
const valid = ref(false);
const passwordValid = ref(false);
const confirmUserDeletion = ref(false);
const activeTab = ref('profile');
const profileImage = ref(props.user.profile_image_url || null);
const loadingUsers = ref(false);

const form = useForm({
  name: props.user.name,
  email: props.user.email,
  phone: props.user.phone || '',
  address: props.user.address || '',
  profile_image: null,
});

const passwordForm = useForm({
  current_password: '',
  password: '',
  password_confirmation: '',
});

const deleteForm = useForm({
  password: '',
});

// Users data
const users = ref(props.users || []);

const rules = {
  required: (value) => !!value || 'This field is required',
  email: (value) => {
    const pattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return pattern.test(value) || 'Enter a valid email address';
  },
  minLength: (value) => {
    return value.length >= 8 || 'Password must be at least 8 characters';
  },
  passwordMatch: (value) => {
    return value === passwordForm.password || 'Passwords do not match';
  },
};

// User table headers
const userHeaders = [
  { title: 'Avatar', key: 'avatar', sortable: false, width: '80px' },
  { title: 'Name', key: 'name', sortable: true },
  { title: 'Email', key: 'email', sortable: true },
  { title: 'Role', key: 'role', sortable: true, width: '100px' },
  { title: 'Status', key: 'status', sortable: true, width: '120px' },
  { title: 'Last Login', key: 'last_login', sortable: true, width: '120px' },
  { title: 'Actions', key: 'actions', sortable: false, width: '80px' }
];

// Computed properties for user statistics
const activeUsers = computed(() => {
  return users.value.filter(user => user.email_verified_at).length;
});

const recentUsers = computed(() => {
  const oneWeekAgo = new Date();
  oneWeekAgo.setDate(oneWeekAgo.getDate() - 7);
  return users.value.filter(user => {
    if (!user.last_login_at) return false;
    return new Date(user.last_login_at) > oneWeekAgo;
  }).length;
});

const adminUsers = computed(() => {
  return users.value.filter(user => user.role === 'admin').length;
});

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
    form.post(route('dashboard.admin.profile.update.post'), {
      forceFormData: true,
      onSuccess: () => {
        // Refresh the page to show updated profile image
        window.location.reload();
      }
    });
  }
};

const updatePassword = () => {
  if (passwordValid.value) {
    passwordForm.put(route('dashboard.admin.password.update'), {
      onSuccess: () => {
        passwordForm.reset();
        // Show success message
      }
    });
  }
};

const handleImageUpload = (event) => {
  const file = event.target.files[0];
  if (file) {
    // Validate file size (2MB max)
    if (file.size > 2 * 1024 * 1024) {
      alert('File size must be less than 2MB');
      return;
    }
    
    // Validate file type
    if (!file.type.startsWith('image/')) {
      alert('Please select an image file');
      return;
    }
    
    // Create preview
    const reader = new FileReader();
    reader.onload = (e) => {
      profileImage.value = e.target.result;
    };
    reader.readAsDataURL(file);
    
    // Set file to form
    form.profile_image = file;
  }
};

const refreshUsers = async () => {
  loadingUsers.value = true;
  try {
    const response = await fetch('/dashboard/api/users');
    const data = await response.json();
    
    if (data.success) {
      users.value = data.users;
    } else {
      console.error('API returned error:', data);
    }
  } catch (error) {
    console.error('Error fetching users:', error);
  } finally {
    loadingUsers.value = false;
  }
};

const viewUser = (user) => {
  router.visit(route('dashboard.users.show', user.uuid));
};

const editUser = (user) => {
  router.visit(route('dashboard.users.edit', user.uuid));
};

const confirmDeleteUser = (user) => {
  if (confirm(`Are you sure you want to delete ${user.name}?`)) {
    router.delete(route('dashboard.users.destroy', user.uuid));
  }
};

const deleteUser = () => {
  deleteForm.delete(route('dashboard.admin.profile.destroy'), {
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

// Load users on component mount
onMounted(() => {
  if (users.value.length === 0) {
    refreshUsers();
  }
});
</script>

<style scoped>
.v-card {
  border-radius: 12px;
}

.v-btn {
  border-radius: 8px;
}
</style>
