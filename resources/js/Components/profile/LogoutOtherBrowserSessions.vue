
<script>

import { ref } from 'vue';
import { useForm } from '@inertiajs/vue3';
import ActionMessage from '@/Components/ActionMessage.vue';
import ActionSection from '@/Components/ActionSection.vue';
import DialogModal from '@/Components/DialogModal.vue';
import InputError from '@/Components/InputError.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { useToast } from 'primevue/usetoast';

export default {
    components: {
        ActionMessage, ActionSection, DialogModal, PrimaryButton, SecondaryButton, TextInput, InputError
    },
    props: {
        sessions: Array,
    },
    setup(props) {
        const toast = useToast();
        const confirmingLogout = ref(false);
        const passwordInput = ref(null);

        const form = useForm({
            password: '',
        });

        const confirmLogout = () => {
            confirmingLogout.value = true;

            setTimeout(() => passwordInput.value.focus(), 250);
        };

        const logoutOtherBrowserSessions = () => {
            form.delete(route('other-browser-sessions.destroy'), {
                preserveScroll: true,
                onSuccess: () => {
                    closeModal()
                    toast.add({ severity: "success", summary: "Sucesso!", detail: "As sessões foram fechadas.", life: 5000 })
                },
                onError: () => {
                    passwordInput.value.focus()
                    toast.add({ severity: "error", summary: "Erro!", detail: "Houve um erro tente novamente mais tarde.", life: 5000 })
                },
                onFinish: () => form.reset(),
            });
        };

        const closeModal = () => {
            confirmingLogout.value = false;

            form.reset();
        };

        return {
            confirmLogout, logoutOtherBrowserSessions, closeModal, form, confirmingLogout, passwordInput
        }
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
                        Sessões de Navegadores
                    </h4>
                    <p class="text-sm lg:mb-6">
                    <div class="max-w-xl text-sm text-gray-600">
                        Se necessário, você pode sair de todas as suas outras sessões do navegador em todos os seus
                        dispositivos.
                        Algumas das suas sessões recentes estão listadas abaixo; no entanto, esta lista pode não ser
                        exaustiva. Se você
                        sentir que sua conta foi comprometida, você também deve atualizar sua senha.
                    </div>
                    </p>
                </div>

                <!-- <button class="danger-button" @click="confirmUserDeletion">
  
            Delete
          </button> -->

                <!-- Other Browser Sessions -->
                <div v-if="sessions.length > 0" class="mt-5 space-y-6">
                    <div v-for="(session, i) in sessions" :key="i" class="flex items-center">
                        <div>
                            <svg v-if="session.agent.is_desktop" class="size-8 text-gray-500"
                                xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M9 17.25v1.007a3 3 0 01-.879 2.122L7.5 21h9l-.621-.621A3 3 0 0115 18.257V17.25m6-12V15a2.25 2.25 0 01-2.25 2.25H5.25A2.25 2.25 0 013 15V5.25m18 0A2.25 2.25 0 0018.75 3H5.25A2.25 2.25 0 003 5.25m18 0V12a2.25 2.25 0 01-2.25 2.25H5.25A2.25 2.25 0 013 12V5.25" />
                            </svg>

                            <svg v-else class="size-8 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none"
                                viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M10.5 1.5H8.25A2.25 2.25 0 006 3.75v16.5a2.25 2.25 0 002.25 2.25h7.5A2.25 2.25 0 0018 20.25V3.75a2.25 2.25 0 00-2.25-2.25H13.5m-3 0V3h3V1.5m-3 0h3m-3 18.75h3" />
                            </svg>
                        </div>

                        <div class="ms-3">
                            <div class="text-sm text-gray-600">
                                {{ session.agent.platform ? session.agent.platform : 'Desconhecido' }} - {{
                                    session.agent.browser ? session.agent.browser : 'Desconhecido' }}
                            </div>

                            <div>
                                <div class="text-xs text-gray-500">
                                    {{ session.ip_address }},

                                    <span v-if="session.is_current_device" class="text-green-500 font-semibold">Este dispositivo</span>
                                    <span v-else>Última vez ativo {{ session.last_active }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="flex items-center mt-5">
                    <button class="edit-button" @click="confirmLogout">
                        Sair de outras sessões de navegador
                    </button>

                    <ActionMessage :on="form.recentlySuccessful" class="ms-3">
                        Pronto.
                    </ActionMessage>
                </div>

                <!-- Log Out Other Devices Confirmation Modal -->
                <DialogModal :show="confirmingLogout" @close="closeModal">
                    <template #title>
                        Sair de outras sessões de navegador
                    </template>

                    <template #content>
                        Insira sua senha para confirmar que você deseja sair de suas outras sessões do navegador
em todos os seus dispositivos.

                        <div class="mt-4">
                            <TextInput ref="passwordInput" v-model="form.password" type="password"
                                class="mt-1 block w-3/4" placeholder="Senha" autocomplete="current-password"
                                @keyup.enter="logoutOtherBrowserSessions" />

                            <InputError :message="form.errors.password" class="mt-2" />
                        </div>
                    </template>

                    <template #footer>
                        <button class="danger-button" @click="closeModal">
                            Cancelar
                        </button>

                        <button class="ms-3 edit-button" :class="{ 'opacity-25': form.processing }"
                            :disabled="form.processing" @click="logoutOtherBrowserSessions">
                            Sair de outras sessões de navegador
                        </button>
                    </template>
                </DialogModal>
            </div>
        </div>

    </div>
</template>
