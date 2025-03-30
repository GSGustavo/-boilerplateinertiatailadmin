<script>


import { ref, computed, watch } from 'vue';
import { router, useForm, usePage } from '@inertiajs/vue3';
import ActionSection from '@/Components/ActionSection.vue';
import ConfirmsPassword from '@/Components/ConfirmsPassword.vue';
import DangerButton from '@/Components/DangerButton.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import axios from 'axios'; // Ensure axios is imported if it's being used

import { useToast } from 'primevue/usetoast';

export default {
    components: {
        ActionSection,
        ConfirmsPassword,
        DangerButton,
        InputError,
        InputLabel,
        PrimaryButton,
        SecondaryButton,
        TextInput
    },
    props: {
        requiresConfirmation: Boolean,
    },
    setup(props) {
        const page = usePage();
        const enabling = ref(false);
        const confirming = ref(false);
        const disabling = ref(false);
        const qrCode = ref(null);
        const setupKey = ref(null);
        const recoveryCodes = ref([]);
        const toast = useToast();

        const confirmationForm = useForm({
            code: '',
        });

        const twoFactorEnabled = computed(() => !enabling.value && page.props.auth.user?.two_factor_enabled);

        watch(twoFactorEnabled, () => {
            if (!twoFactorEnabled.value) {
                confirmationForm.reset();
                confirmationForm.clearErrors();
            }
        });

        const enableTwoFactorAuthentication = () => {
            enabling.value = true;
            router.post(route('two-factor.enable'), {}, {
                preserveScroll: true,
                onSuccess: () => Promise.all([
                    showQrCode(),
                    showSetupKey(),
                    showRecoveryCodes(),
                ]),
                onFinish: () => {
                    enabling.value = false;
                    confirming.value = props.requiresConfirmation;
                },
            });
        };

        const showQrCode = () => axios.get(route('two-factor.qr-code')).then(response => {
            qrCode.value = response.data.svg;
        });

        const showSetupKey = () => axios.get(route('two-factor.secret-key')).then(response => {
            setupKey.value = response.data.secretKey;
        });

        const showRecoveryCodes = () => axios.get(route('two-factor.recovery-codes')).then(response => {
            recoveryCodes.value = response.data;
        });

        const confirmTwoFactorAuthentication = () => {
            confirmationForm.post(route('two-factor.confirm'), {
                errorBag: "confirmTwoFactorAuthentication",
                preserveScroll: true,
                preserveState: true,
                onSuccess: () => {
                    confirming.value = false;
                    qrCode.value = null;
                    setupKey.value = null;
                },
            });
        };

        const regenerateRecoveryCodes = () => {
            axios.post(route('two-factor.recovery-codes')).then(() => showRecoveryCodes());
        };

        const disableTwoFactorAuthentication = () => {
            disabling.value = true;
            router.delete(route('two-factor.disable'), {
                preserveScroll: true,
                onSuccess: () => {
                    disabling.value = false;
                    confirming.value = false;
                },
            });
        };

        return {
            // All the reactive data, computed properties, and methods used in the template should be returned here
            enabling, confirming, disabling, qrCode, setupKey, recoveryCodes,
            enableTwoFactorAuthentication, showQrCode, showSetupKey, showRecoveryCodes,
            confirmTwoFactorAuthentication, regenerateRecoveryCodes, disableTwoFactorAuthentication,
            twoFactorEnabled, confirmationForm
        };
    }
}

</script>


<template>
  <Toast/>
  <div>
    <div class="p-5 mb-6 border border-gray-200 rounded-2xl dark:border-gray-800 lg:p-6">
      <div class="flex flex-col gap-6 lg:flex-row lg:items-start lg:justify-between">
        <div>
          <h4 class="text-lg font-semibold text-gray-800 dark:text-white/90 ">
            Autenticação de Dois Fatores

          </h4>

          <p class="text-sm lg:mb-6">
            Adicione uma segurança adicional em sua conta utilizando a autenticação de dois fatores.
          </p>

          <div>
            <h3 v-if="twoFactorEnabled && !confirming" class="text-lg font-medium text-gray-900">
              Você habilitou a autenticação de dois fatores.
            </h3>

            <h3 v-else-if="twoFactorEnabled && confirming" class="text-lg font-medium text-gray-900">
              Terminando a ativação da autenticação de dois fatores.
            </h3>

            <h3 v-else class="text-lg font-medium text-gray-900">
              Você não habilitou a autenticação de dois fatores.
            </h3>

            <div class="mt-3 max-w-xl text-sm text-gray-600">
              <p>
                Quando a autenticação de dois fatores estiver habilitada, você será solicitado a fornecer um token seguro e aleatório durante
                a autenticação. Você pode recuperar esse token do aplicativo Google Authenticator do seu telefone.
              </p>
            </div>
          </div>

          <div v-if="twoFactorEnabled">
            <div v-if="qrCode">
              <div class="mt-4 max-w-xl text-sm text-gray-600">
                <p v-if="confirming" class="font-semibold">
                  Para concluir a ativação da autenticação de dois fatores, escaneie o seguinte código QR usando o aplicativo autenticador do seu telefone ou insira a chave de configuração e forneça o código OTP gerado.
                </p>

                <p v-else>
                  A autenticação de dois fatores agora está habilitada. Escaneie o seguinte código QR usando o aplicativo autenticador
                  do seu telefone ou insira a chave de configuração.
                </p>
              </div>

              <div class="mt-4 p-2 inline-block bg-white" v-html="qrCode" />

              <div v-if="setupKey" class="mt-4 max-w-xl text-sm text-gray-600">
                <p class="font-semibold">
                  Chave de Configuração: <span v-html="setupKey"></span>
                </p>
              </div>

              <div v-if="confirming" class="mt-4">
                <InputLabel for="code" value="Code" />

                <TextInput id="code" v-model="confirmationForm.code" type="text" name="code" class="block mt-1 w-1/2"
                  inputmode="numeric" autofocus autocomplete="one-time-code"
                  @keyup.enter="confirmTwoFactorAuthentication" />

                <InputError :message="confirmationForm.errors.code" class="mt-2" />
              </div>
            </div>

            <div v-if="recoveryCodes.length > 0 && !confirming">
              <div class="mt-4 max-w-xl text-sm text-gray-600">
                <p class="font-semibold">
                  Armazene esses códigos de recuperação em um gerenciador de senhas seguro. Eles podem ser usados ​​para recuperar o acesso à sua
                  conta se seu dispositivo de autenticação de dois fatores for perdido.
                </p>
              </div>

              <div class="grid gap-1 max-w-xl mt-4 px-4 py-4 font-mono text-sm bg-gray-100 rounded-lg">
                <div v-for="code in recoveryCodes" :key="code">
                  {{ code }}
                </div>
              </div>
            </div>
          </div>
        </div>


        <div class="mt-5">
          <div v-if="!twoFactorEnabled">
            <ConfirmsPassword @confirmed="enableTwoFactorAuthentication">
              <button class="edit-button" type="button" :class="{ 'opacity-25': enabling }" :disabled="enabling">
                Habilitar
              </button>

            </ConfirmsPassword>
          </div>

          <div v-else>
            <ConfirmsPassword @confirmed="confirmTwoFactorAuthentication">
              <button v-if="confirming" class="edit-button me-3" type="button" :class="{ 'opacity-25': enabling }"
                :disabled="enabling">
                Confirm
              </button>
            </ConfirmsPassword>

            <ConfirmsPassword @confirmed="regenerateRecoveryCodes">
              <button v-if="recoveryCodes.length > 0 && !confirming" class="edit-button me-3" type="submit"
                :class="{ 'opacity-25': enabling }" :disabled="enabling">
                Re-gerar códigos de recuperação

              </button>
            </ConfirmsPassword>

            <ConfirmsPassword @confirmed="showRecoveryCodes">
              <button v-if="recoveryCodes.length === 0 && !confirming" class="edit-button me-3" type="submit"
                :class="{ 'opacity-25': enabling }" :disabled="enabling">
                Mostrar Códigos de Recuperação
              </button>
            </ConfirmsPassword>

            <ConfirmsPassword @confirmed="disableTwoFactorAuthentication">

              <button v-if="confirming" class="danger-button" type="submit" :class="{ 'opacity-25': enabling }"
                :disabled="enabling">
                Cancelar
              </button>
            </ConfirmsPassword>

            <ConfirmsPassword @confirmed="disableTwoFactorAuthentication">

              <button v-if="!confirming" class="danger-button" type="submit"
                :class="{ 'opacity-25': enabling }" :disabled="enabling">
                Cancelar
              </button>
            </ConfirmsPassword>
          </div>
        </div>
      </div>
    </div>

  </div>
</template>

