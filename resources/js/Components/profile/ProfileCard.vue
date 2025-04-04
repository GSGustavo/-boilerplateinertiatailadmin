<script>
import { ref } from 'vue';
import { Link, useForm, usePage, router } from '@inertiajs/vue3';
import ActionMessage from '@/Components/ActionMessage.vue';
import FormSection from '@/Components/FormSection.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { useToast } from 'primevue/usetoast';


export default {
	components: {
		ActionMessage,
		FormSection,
		InputError,
		InputLabel,
		PrimaryButton,
		SecondaryButton,
		TextInput,
		Link,
	},
	setup() {
		
		const toast = useToast();
		const user = usePage().props.auth.user;


		const form = useForm({
			_method: 'PUT',
			name: user.name,
			email: user.email,
			photo: null,
		});

		const verificationLinkSent = ref(false);
		const photoPreview = ref(null);
		const photoInput = ref(null);

		const updateProfileInformation = () => {
			if (photoInput.value) {
				form.photo = photoInput.value.files[0];
			}

			form.post(route('user-profile-information.update'), {
				errorBag: 'updateProfileInformation',
				preserveScroll: true,
				onSuccess: () => {
					clearPhotoFileInput();
					isProfileInfoModal.value = false;
					toast.add({ severity: "success", summary: "Sucesso!", detail: "As informações foram atualizadas.", life: 5000 })
				},
				onError: () => {
					toast.add({ severity: "error", summary: "Erro!", detail: "Houve um erro tente novamente mais tarde.", life: 5000 })
				}
			});

		};

		const sendEmailVerification = () => {
			verificationLinkSent.value = true;
		};

		const selectNewPhoto = () => {
			photoInput.value.click();
		};

		const updatePhotoPreview = () => {
			const photo = photoInput.value.files[0];

			if (!photo) return;

			const reader = new FileReader();

			reader.onload = (e) => {
				photoPreview.value = e.target.result;
			};

			reader.readAsDataURL(photo);
		};

		const deletePhoto = () => {
			router.delete(route('current-user-photo.destroy'), {
				preserveScroll: true,
				onSuccess: () => {
					photoPreview.value = null;
					clearPhotoFileInput();
				},
			});
		};

		const clearPhotoFileInput = () => {
			if (photoInput.value?.value) {
				photoInput.value.value = null;
			}
		};

		const isProfileInfoModal = ref(false);

		return {
			form,
			verificationLinkSent,
			photoPreview,
			photoInput,
			updateProfileInformation,
			sendEmailVerification,
			selectNewPhoto,
			updatePhotoPreview,
			deletePhoto,
			clearPhotoFileInput,
			isProfileInfoModal,
			user
		};
	},
};
</script>

<template>
	<Toast/>
	<div>
		<div class="p-5 mb-6 border border-gray-200 rounded-2xl dark:border-gray-800 lg:p-6">
			<div class="flex flex-col gap-5 xl:flex-row xl:items-center xl:justify-between">
				<div class="flex flex-col items-center w-full gap-6 xl:flex-row">
					<div class="w-20 h-20 overflow-hidden border border-gray-200 rounded-full dark:border-gray-800">
						<img class="w-full h-full m-auto" :src="$page.props.auth.user.profile_photo_url" alt="user" />
					</div>
					<div class="order-3 xl:order-2">
						<h4
							class="mb-2 text-lg font-semibold text-center text-gray-800 dark:text-white/90 xl:text-left">
							{{ $page.props.auth.user.name }}
						</h4>
						<div class="flex flex-col items-center gap-1 text-center xl:flex-row xl:gap-3 xl:text-left">
							<p class="text-sm text-gray-500 dark:text-gray-400">Team Manager</p>
							<div class="hidden h-3.5 w-px bg-gray-300 dark:bg-gray-700 xl:block"></div>
							<p class="text-sm text-gray-500 dark:text-gray-400">Arizona, United States</p>
						</div>
					</div>
					<div class="flex items-center order-2 gap-2 grow xl:order-3 xl:justify-end">
						<a href="https://www.facebook.com/PimjoHQ" target="_blank" rel="noopener" class="social-button">
							<svg class="fill-current" width="20" height="20" viewBox="0 0 20 20" fill="none"
								xmlns="http://www.w3.org/2000/svg">
								<path
									d="M11.6666 11.2503H13.7499L14.5833 7.91699H11.6666V6.25033C11.6666 5.39251 11.6666 4.58366 13.3333 4.58366H14.5833V1.78374C14.3118 1.7477 13.2858 1.66699 12.2023 1.66699C9.94025 1.66699 8.33325 3.04771 8.33325 5.58342V7.91699H5.83325V11.2503H8.33325V18.3337H11.6666V11.2503Z"
									fill="" />
							</svg>
						</a>
						<a href="https://x.com/PimjoHQ" target="_blank" rel="noopener" class="social-button">
							<svg class="fill-current" width="20" height="20" viewBox="0 0 20 20" fill="none"
								xmlns="http://www.w3.org/2000/svg">
								<path
									d="M15.1708 1.875H17.9274L11.9049 8.75833L18.9899 18.125H13.4424L9.09742 12.4442L4.12578 18.125H1.36745L7.80912 10.7625L1.01245 1.875H6.70078L10.6283 7.0675L15.1708 1.875ZM14.2033 16.475H15.7308L5.87078 3.43833H4.23162L14.2033 16.475Z"
									fill="" />
							</svg>
						</a>
						<a href="https://www.linkedin.com/company/pimjo/" target="_blank" rel="noopener"
							class="social-button">
							<svg class="fill-current" width="20" height="20" viewBox="0 0 20 20" fill="none"
								xmlns="http://www.w3.org/2000/svg">
								<path
									d="M5.78381 4.16645C5.78351 4.84504 5.37181 5.45569 4.74286 5.71045C4.11391 5.96521 3.39331 5.81321 2.92083 5.32613C2.44836 4.83904 2.31837 4.11413 2.59216 3.49323C2.86596 2.87233 3.48886 2.47942 4.16715 2.49978C5.06804 2.52682 5.78422 3.26515 5.78381 4.16645ZM5.83381 7.06645H2.50048V17.4998H5.83381V7.06645ZM11.1005 7.06645H7.78381V17.4998H11.0672V12.0248C11.0672 8.97475 15.0422 8.69142 15.0422 12.0248V17.4998H18.3338V10.8914C18.3338 5.74978 12.4505 5.94145 11.0672 8.46642L11.1005 7.06645Z"
									fill="" />
							</svg>
						</a>
						<a href="https://www.instagram.com/PimjoHQ" target="_blank" rel="noopener"
							class="social-button">
							<svg class="fill-current" width="20" height="20" viewBox="0 0 20 20" fill="none"
								xmlns="http://www.w3.org/2000/svg">
								<path
									d="M10.8567 1.66699C11.7946 1.66854 12.2698 1.67351 12.6805 1.68573L12.8422 1.69102C13.0291 1.69766 13.2134 1.70599 13.4357 1.71641C14.3224 1.75738 14.9273 1.89766 15.4586 2.10391C16.0078 2.31572 16.4717 2.60183 16.9349 3.06503C17.3974 3.52822 17.6836 3.99349 17.8961 4.54141C18.1016 5.07197 18.2419 5.67753 18.2836 6.56433C18.2935 6.78655 18.3015 6.97088 18.3081 7.15775L18.3133 7.31949C18.3255 7.73011 18.3311 8.20543 18.3328 9.1433L18.3335 9.76463C18.3336 9.84055 18.3336 9.91888 18.3336 9.99972L18.3335 10.2348L18.333 10.8562C18.3314 11.794 18.3265 12.2694 18.3142 12.68L18.3089 12.8417C18.3023 13.0286 18.294 13.213 18.2836 13.4351C18.2426 14.322 18.1016 14.9268 17.8961 15.458C17.6842 16.0074 17.3974 16.4713 16.9349 16.9345C16.4717 17.397 16.0057 17.6831 15.4586 17.8955C14.9273 18.1011 14.3224 18.2414 13.4357 18.2831C13.2134 18.293 13.0291 18.3011 12.8422 18.3076L12.6805 18.3128C12.2698 18.3251 11.7946 18.3306 10.8567 18.3324L10.2353 18.333C10.1594 18.333 10.0811 18.333 10.0002 18.333H9.76516L9.14375 18.3325C8.20591 18.331 7.7306 18.326 7.31997 18.3137L7.15824 18.3085C6.97136 18.3018 6.78703 18.2935 6.56481 18.2831C5.67801 18.2421 5.07384 18.1011 4.5419 17.8955C3.99328 17.6838 3.5287 17.397 3.06551 16.9345C2.60231 16.4713 2.3169 16.0053 2.1044 15.458C1.89815 14.9268 1.75856 14.322 1.7169 13.4351C1.707 13.213 1.69892 13.0286 1.69238 12.8417L1.68714 12.68C1.67495 12.2694 1.66939 11.794 1.66759 10.8562L1.66748 9.1433C1.66903 8.20543 1.67399 7.73011 1.68621 7.31949L1.69151 7.15775C1.69815 6.97088 1.70648 6.78655 1.7169 6.56433C1.75786 5.67683 1.89815 5.07266 2.1044 4.54141C2.3162 3.9928 2.60231 3.52822 3.06551 3.06503C3.5287 2.60183 3.99398 2.31641 4.5419 2.10391C5.07315 1.89766 5.67731 1.75808 6.56481 1.71641C6.78703 1.70652 6.97136 1.69844 7.15824 1.6919L7.31997 1.68666C7.7306 1.67446 8.20591 1.6689 9.14375 1.6671L10.8567 1.66699ZM10.0002 5.83308C7.69781 5.83308 5.83356 7.69935 5.83356 9.99972C5.83356 12.3021 7.69984 14.1664 10.0002 14.1664C12.3027 14.1664 14.1669 12.3001 14.1669 9.99972C14.1669 7.69732 12.3006 5.83308 10.0002 5.83308ZM10.0002 7.49974C11.381 7.49974 12.5002 8.61863 12.5002 9.99972C12.5002 11.3805 11.3813 12.4997 10.0002 12.4997C8.6195 12.4997 7.50023 11.3809 7.50023 9.99972C7.50023 8.61897 8.61908 7.49974 10.0002 7.49974ZM14.3752 4.58308C13.8008 4.58308 13.3336 5.04967 13.3336 5.62403C13.3336 6.19841 13.8002 6.66572 14.3752 6.66572C14.9496 6.66572 15.4169 6.19913 15.4169 5.62403C15.4169 5.04967 14.9488 4.58236 14.3752 4.58308Z"
									fill="" />
							</svg>
						</a>
					</div>
				</div>
				<button @click="isProfileInfoModal = true" class="edit-button">
					<svg class="fill-current" width="18" height="18" viewBox="0 0 18 18" fill="none"
						xmlns="http://www.w3.org/2000/svg">
						<path fill-rule="evenodd" clip-rule="evenodd"
							d="M15.0911 2.78206C14.2125 1.90338 12.7878 1.90338 11.9092 2.78206L4.57524 10.116C4.26682 10.4244 4.0547 10.8158 3.96468 11.2426L3.31231 14.3352C3.25997 14.5833 3.33653 14.841 3.51583 15.0203C3.69512 15.1996 3.95286 15.2761 4.20096 15.2238L7.29355 14.5714C7.72031 14.4814 8.11172 14.2693 8.42013 13.9609L15.7541 6.62695C16.6327 5.74827 16.6327 4.32365 15.7541 3.44497L15.0911 2.78206ZM12.9698 3.84272C13.2627 3.54982 13.7376 3.54982 14.0305 3.84272L14.6934 4.50563C14.9863 4.79852 14.9863 5.2734 14.6934 5.56629L14.044 6.21573L12.3204 4.49215L12.9698 3.84272ZM11.2597 5.55281L5.6359 11.1766C5.53309 11.2794 5.46238 11.4099 5.43238 11.5522L5.01758 13.5185L6.98394 13.1037C7.1262 13.0737 7.25666 13.003 7.35947 12.9002L12.9833 7.27639L11.2597 5.55281Z"
							fill="" />
					</svg>
					Editar
				</button>
			</div>
		</div>

		<Dialog v-model:visible="isProfileInfoModal" :style="{ width: '650px' }" header="Editar Informações"
			class="z-9999999" :modal="true">

			<div
				class="no-scrollbar relative w-full max-w-[700px] overflow-y-auto rounded-3xl bg-white p-4 dark:bg-gray-900 ">
				<form class="flex flex-col">
					<div class="custom-scrollbar h-[458px] overflow-y-auto p-2">

						<div v-if="$page.props.jetstream.canUpdateProfileInformation">
							<div v-if="$page.props.jetstream.managesProfilePhotos" class="col-span-6 sm:col-span-4">
								<!-- Profile Photo File Input -->
								<input id="photo" ref="photoInput" type="file" class="hidden"
									@change="updatePhotoPreview">

								<InputLabel for="photo" value="Foto" />

								<!-- Current Profile Photo -->
								<div v-show="!photoPreview" class="mt-2">
									<img :src="user.profile_photo_url" :alt="user.name"
										class="rounded-full size-20 object-cover">
								</div>

								<!-- New Profile Photo Preview -->
								<div v-show="photoPreview" class="mt-2">
									<span class="block rounded-full size-20 bg-cover bg-no-repeat bg-center"
										:style="'background-image: url(\'' + photoPreview + '\');'" />
								</div>

								<SecondaryButton class="mt-2 me-2" type="button" @click.prevent="selectNewPhoto">
									Selecionar Foto
								</SecondaryButton>

								<SecondaryButton v-if="user.profile_photo_path" type="button" class="mt-2"
									@click.prevent="deletePhoto">
									Remover Foto
								</SecondaryButton>

								<InputError :message="form.errors.photo" class="mt-2" />
							</div>
						</div>
						<div class="mt-7">
							<h5 class="mb-5 text-lg font-medium text-gray-800 dark:text-white/90 lg:mb-6">
								Informações Pessoais
							</h5>

							<div class="grid grid-cols-1 gap-x-6 gap-y-5 lg:grid-cols-2">
								<div class="col-span-2 lg:col-span-1">
									<label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
										Nome Completo
									</label>
									<input v-model="form.name" type="text"
										class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent bg-none px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800" />
									<InputError :message="form.errors.name" class="mt-2" />
								</div>

								<div class="col-span-2 lg:col-span-1">
									<label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
										Endereço de Email
									</label>
									<input type="text" v-model="form.email"
										class="dark:bg-dark-900 h-11 w-full appearance-none rounded-lg border border-gray-300 bg-transparent bg-none px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800" />
									<InputError :message="form.errors.email" class="mt-2" />
								</div>
							</div>
						</div>

					</div>
				</form>
			</div>

			<template #footer>
				<Button label="Cancelar" icon="pi pi-times" text @click="isProfileInfoModal = false" />
				<Button type="submit" label="Salvar" icon="pi pi-check" @click="updateProfileInformation" />
			</template>
		</Dialog>
	</div>
</template>
