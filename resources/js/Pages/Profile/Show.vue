<script>
import AppLayout from '@/Layouts/AppLayout.vue';
import DeleteUserForm from '@/Pages/Profile/Partials/DeleteUserForm.vue';
import LogoutOtherBrowserSessionsForm from '@/Pages/Profile/Partials/LogoutOtherBrowserSessionsForm.vue';
import SectionBorder from '@/Components/SectionBorder.vue';
import TwoFactorAuthenticationForm from '@/Pages/Profile/Partials/TwoFactorAuthenticationForm.vue';
import UpdatePasswordForm from '@/Pages/Profile/Partials/UpdatePasswordForm.vue';
import UpdateProfileInformationForm from '@/Pages/Profile/Partials/UpdateProfileInformationForm.vue';
import MainAdmin from '@/Components/layout/MainAdmin.vue';
import PageBreadcrumb from '@/Components/common/PageBreadcrumb.vue'
import ProfileCard from '@/Components/profile/ProfileCard.vue'
import PasswordCard from '@/Components/profile/PasswordCard.vue';
import TwoFactorCard from '@/Components/profile/TwoFactorCard.vue';
import DeleteAccount from '@/Components/profile/DeleteAccount.vue';
import LogoutOtherBrowserSessions from '@/Components/profile/LogoutOtherBrowserSessions.vue';

export default {
  components: {
    DeleteUserForm,
    // LogoutOtherBrowserSessionsForm,
    SectionBorder,
    // TwoFactorAuthenticationForm,
    // UpdatePasswordForm,
    // UpdateProfileInformationForm,
    PageBreadcrumb,
    ProfileCard,
    PasswordCard,
    TwoFactorCard,
    DeleteAccount,
    LogoutOtherBrowserSessions
  },
  props: {
    confirmsTwoFactorAuthentication: Boolean,
    sessions: Array
  },


  layout: MainAdmin
}
</script>

<template>
  <PageBreadcrumb pageTitle="User Profile" />
  <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03] lg:p-6">
    <h3 class="mb-5 text-lg font-semibold text-gray-800 dark:text-white/90 lg:mb-7">Profile</h3>
    <div v-if="$page.props.jetstream.canUpdateProfileInformation">
      <ProfileCard />

      <SectionBorder />
    </div>

    <div v-if="$page.props.jetstream.canUpdatePassword">
      <password-card></password-card>

                    <SectionBorder />
                </div>
    

    <div v-if="$page.props.jetstream.canManageTwoFactorAuthentication">
      <TwoFactorCard :requires-confirmation="confirmsTwoFactorAuthentication" class="mt-10 sm:mt-0" />
    </div>

    <LogoutOtherBrowserSessions :sessions="sessions" />

    <template v-if="$page.props.jetstream.hasAccountDeletionFeatures">
      <SectionBorder />

      <DeleteAccount class="mt-10 sm:mt-0" />
    </template>

  </div>
</template>