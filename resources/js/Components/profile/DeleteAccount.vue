<template>
  <div>
    <div class="p-5 mb-6 border border-gray-200 rounded-2xl dark:border-gray-800 lg:p-6">
      <div class="flex flex-col gap-6 lg:flex-row lg:items-start lg:justify-between">
        <div>
          <h4 class="text-lg font-semibold text-gray-800 dark:text-white/90 ">
            Delete Account
          </h4>

          <p class="text-sm">Permanently delete your account.</p>


          <p class="text-sm lg:mb-6">
          <div class="max-w-xl text-sm text-gray-600">
            Once your account is deleted, all of its resources and data will be permanently deleted. Before deleting
            your account, please download any data or information that you wish to retain.
          </div>
          </p>
        </div>

        <button class="danger-button" @click="confirmUserDeletion">

          Delete
        </button>

        <DialogModal :show="confirmingUserDeletion" @close="closeModal">
          <template #title>
            Delete Account
          </template>

          <template #content>
            Are you sure you want to delete your account? Once your account is deleted, all of its resources and data
            will be permanently deleted. Please enter your password to confirm you would like to permanently delete your
            account.

            <div class="mt-4">
              <TextInput ref="passwordInput" v-model="form.password" type="password" class="mt-1 block w-3/4"
                placeholder="Password" autocomplete="current-password" @keyup.enter="deleteUser" />

              <InputError :message="form.errors.password" class="mt-2" />
            </div>
          </template>

          <template #footer>
            <SecondaryButton @click="closeModal">
              Cancel
            </SecondaryButton>

            <DangerButton class="ms-3" :class="{ 'opacity-25': form.processing }" :disabled="form.processing"
              @click="deleteUser">
              Delete Account
            </DangerButton>
          </template>
        </DialogModal>
      </div>
    </div>

  </div>
</template>

<script>

import { ref } from 'vue';
import { useForm } from '@inertiajs/vue3';
import ActionSection from '@/Components/ActionSection.vue';
import DangerButton from '@/Components/DangerButton.vue';
import DialogModal from '@/Components/DialogModal.vue';
import InputError from '@/Components/InputError.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import TextInput from '@/Components/TextInput.vue';

export default {
  components: {
    ActionSection,
    DangerButton,
    DialogModal,
    SecondaryButton,
    TextInput
  },
  setup() {
    const confirmingUserDeletion = ref(false);
    const passwordInput = ref(null);

    const form = useForm({
      password: '',
    });

    const confirmUserDeletion = () => {
      confirmingUserDeletion.value = true;

      setTimeout(() => passwordInput.value.focus(), 250);
    };

    const deleteUser = () => {
      form.delete(route('current-user.destroy'), {
        preserveScroll: true,
        onSuccess: () => closeModal(),
        onError: () => passwordInput.value.focus(),
        onFinish: () => form.reset(),
      });
    };

    const closeModal = () => {
      confirmingUserDeletion.value = false;

      form.reset();
    };

    return {
      confirmingUserDeletion, passwordInput, form, deleteUser, closeModal, confirmUserDeletion
    }
  }
}



</script>
