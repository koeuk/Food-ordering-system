<template>
  <DashboardLayout>
    <Head title="Add Supplier" />

    <v-container>
      <!-- Header -->
      <div class="d-flex justify-space-between align-center mb-6">
        <div>
          <h1 class="text-h3 font-weight-bold text-grey-darken-3 mb-2">
            Add Supplier
          </h1>
          <p class="text-grey-darken-1">
            Add a new supplier to your system
          </p>
        </div>
        <v-btn
          color="grey"
          variant="outlined"
          :to="{ name: 'dashboard.suppliers.index' }"
        >
          <v-icon left>mdi-arrow-left</v-icon>
          Back to Suppliers
        </v-btn>
      </div>

      <!-- Create Form -->
      <v-card elevation="2">
        <v-card-title class="text-h6 font-weight-bold text-grey-darken-3">
          <v-icon left color="primary">mdi-plus</v-icon>
          Supplier Information
        </v-card-title>
        <v-card-text>
          <v-form ref="form" v-model="valid">
            <v-row>
              <!-- Company Name -->
              <v-col cols="12" md="6">
                <v-text-field
                  v-model="form.company_name"
                  label="Company Name"
                  variant="outlined"
                  :rules="[rules.required]"
                  required
                />
              </v-col>

              <!-- Contact Person -->
              <v-col cols="12" md="6">
                <v-text-field
                  v-model="form.contact_person"
                  label="Contact Person"
                  variant="outlined"
                  :rules="[rules.required]"
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
                  required
                />
              </v-col>

              <!-- Phone -->
              <v-col cols="12" md="6">
                <v-text-field
                  v-model="form.phone"
                  label="Phone"
                  variant="outlined"
                  :rules="[rules.required]"
                  required
                />
              </v-col>

              <!-- Address -->
              <v-col cols="12">
                <v-textarea
                  v-model="form.address"
                  label="Address"
                  variant="outlined"
                  rows="3"
                  :rules="[rules.required]"
                  required
                />
              </v-col>

              <!-- City -->
              <v-col cols="12" md="4">
                <v-text-field
                  v-model="form.city"
                  label="City"
                  variant="outlined"
                  :rules="[rules.required]"
                  required
                />
              </v-col>

              <!-- State -->
              <v-col cols="12" md="4">
                <v-text-field
                  v-model="form.state"
                  label="State"
                  variant="outlined"
                  :rules="[rules.required]"
                  required
                />
              </v-col>

              <!-- Postal Code -->
              <v-col cols="12" md="4">
                <v-text-field
                  v-model="form.postal_code"
                  label="Postal Code"
                  variant="outlined"
                  :rules="[rules.required]"
                  required
                />
              </v-col>

              <!-- Country -->
              <v-col cols="12" md="6">
                <v-text-field
                  v-model="form.country"
                  label="Country"
                  variant="outlined"
                  :rules="[rules.required]"
                  required
                />
              </v-col>

              <!-- Website -->
              <v-col cols="12" md="6">
                <v-text-field
                  v-model="form.website"
                  label="Website"
                  variant="outlined"
                  prepend-inner-icon="mdi-web"
                />
              </v-col>

              <!-- Payment Terms -->
              <v-col cols="12" md="6">
                <v-select
                  v-model="form.payment_terms"
                  :items="paymentTermsOptions"
                  label="Payment Terms"
                  variant="outlined"
                  :rules="[rules.required]"
                  required
                />
              </v-col>

              <!-- Credit Limit -->
              <v-col cols="12" md="6">
                <v-text-field
                  v-model="form.credit_limit"
                  label="Credit Limit"
                  type="number"
                  step="0.01"
                  prefix="$"
                  variant="outlined"
                />
              </v-col>

              <!-- Notes -->
              <v-col cols="12">
                <v-textarea
                  v-model="form.notes"
                  label="Notes"
                  variant="outlined"
                  rows="3"
                  hint="Additional notes about this supplier"
                  persistent-hint
                />
              </v-col>

              <!-- Status -->
              <v-col cols="12">
                <v-switch
                  v-model="form.is_active"
                  label="Active Supplier"
                  color="primary"
                  hint="Inactive suppliers won't appear in new orders"
                  persistent-hint
                />
              </v-col>
            </v-row>

            <!-- Form Actions -->
            <v-row>
              <v-col cols="12">
                <div class="d-flex gap-4">
                  <v-btn
                    color="primary"
                    size="large"
                    :disabled="!valid"
                    @click="submitForm"
                    :loading="loading"
                  >
                    <v-icon left>mdi-check</v-icon>
                    Add Supplier
                  </v-btn>
                  <v-btn
                    color="grey"
                    variant="outlined"
                    size="large"
                    :to="{ name: 'dashboard.suppliers.index' }"
                  >
                    Cancel
                  </v-btn>
                </div>
              </v-col>
            </v-row>
          </v-form>
        </v-card-text>
      </v-card>
    </v-container>
  </DashboardLayout>
</template>

<script setup>
import { ref } from 'vue';
import { Head, router } from '@inertiajs/vue3';
import DashboardLayout from '@/Layouts/DashboardLayout.vue';

const form = ref({
  company_name: '',
  contact_person: '',
  email: '',
  phone: '',
  address: '',
  city: '',
  state: '',
  postal_code: '',
  country: '',
  website: '',
  payment_terms: 'net_30',
  credit_limit: '',
  notes: '',
  is_active: true
});

const valid = ref(false);
const loading = ref(false);

const paymentTermsOptions = [
  { title: 'Net 15', value: 'net_15' },
  { title: 'Net 30', value: 'net_30' },
  { title: 'Net 45', value: 'net_45' },
  { title: 'Net 60', value: 'net_60' },
  { title: 'Cash on Delivery', value: 'cod' },
  { title: 'Prepaid', value: 'prepaid' }
];

const rules = {
  required: (value) => !!value || 'This field is required',
  email: (value) => {
    const pattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return pattern.test(value) || 'Enter a valid email address';
  }
};

const submitForm = () => {
  if (valid.value) {
    loading.value = true;
    
    // Create a plain object copy to avoid circular references
    const supplierData = {
      company_name: form.value.company_name,
      contact_person: form.value.contact_person,
      email: form.value.email,
      phone: form.value.phone,
      address: form.value.address,
      city: form.value.city,
      state: form.value.state,
      postal_code: form.value.postal_code,
      country: form.value.country,
      website: form.value.website,
      payment_terms: form.value.payment_terms,
      credit_limit: form.value.credit_limit,
      notes: form.value.notes,
      is_active: form.value.is_active
    };
    
    router.post(route('dashboard.suppliers.store'), supplierData, {
      onSuccess: () => {
        // Supplier created successfully
      },
      onError: () => {
        loading.value = false;
      },
      onFinish: () => {
        loading.value = false;
      }
    });
  }
};
</script>

