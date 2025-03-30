
<script>

import { ref } from 'vue';
import { useForm } from '@inertiajs/vue3';
import ActionSection from '@/Components/ActionSection.vue';
import DangerButton from '@/Components/DangerButton.vue';
import DialogModal from '@/Components/DialogModal.vue';
import InputError from '@/Components/InputError.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { useToast } from 'primevue/usetoast';

export default {
  components: {
    ActionSection,
    DangerButton,
    DialogModal,
    SecondaryButton,
    TextInput
  },
  setup() {
    const toast = useToast();
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
        onSuccess: () => {
          closeModal()
          toast.add({ severity: "success", summary: "Sucesso!", detail: "A sua conta foi excluída.", life: 5000 })
        },
        onError: () => {
          passwordInput.value.focus()
          toast.add({ severity: "error", summary: "Erro!", detail: "Houve um erro tente novamente mais tarde.", life: 5000 })
        },
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


<template>
  <div>
    <div class="p-5 mb-6 border border-gray-200 rounded-2xl dark:border-gray-800 lg:p-6">
      <div class="flex flex-col gap-6 lg:flex-row lg:items-start lg:justify-between">
        <div>
          <h4 class="text-lg font-semibold text-gray-800 dark:text-white/90 ">
            Apagar Conta
          </h4>

          <p class="text-sm">Apagar permanentemente sua conta.</p>


          <p class="text-sm lg:mb-6">
          <div class="max-w-xl text-sm text-gray-600">
            Depois que sua conta for excluída, todos os seus recursos e dados serão excluídos permanentemente. Antes de excluir
sua conta, baixe quaisquer dados ou informações que você deseja reter.
          </div>
          </p>
        </div>

        <button class="danger-button" @click="confirmUserDeletion">

          Apagar
        </button>

        <DialogModal :show="confirmingUserDeletion" @close="closeModal">
          <template #title>
            Apagar Conta
          </template>

          <template #content>
            Tem certeza de que deseja excluir sua conta? Depois que sua conta for excluída, todos os seus recursos e dados
serão excluídos permanentemente. Insira sua senha para confirmar que você deseja excluir permanentemente sua
conta.

            <div class="mt-4">
              <TextInput ref="passwordInput" v-model="form.password" type="password" class="mt-1 block w-3/4"
                placeholder="Senha" autocomplete="current-password" @keyup.enter="deleteUser" />

              <InputError :message="form.errors.password" class="mt-2" />
            </div>
          </template>

          <template #footer>
            <SecondaryButton @click="closeModal">
              Cancelar
            </SecondaryButton>

            <DangerButton class="ms-3" :class="{ 'opacity-25': form.processing }" :disabled="form.processing"
              @click="deleteUser">
              Apagar Conta
            </DangerButton>
          </template>
        </DialogModal>
      </div>
    </div>

  </div>
</template>
