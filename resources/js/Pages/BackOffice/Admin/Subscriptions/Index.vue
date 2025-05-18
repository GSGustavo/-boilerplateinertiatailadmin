<script>

import { ref, reactive, onMounted } from 'vue'
import { Link, router, Head } from '@inertiajs/vue3';
import MainAdmin from '@/Components/layout/MainAdmin.vue';
import Modal from '@/Components/profile/Modal.vue'
import PageBreadcrumb from '@/Components/common/PageBreadcrumb.vue'
import { FilterMatchMode } from '@primevue/core/api';
import { useToast } from 'primevue/usetoast';
import { zodResolver } from '@primevue/forms/resolvers/zod';
import { z } from 'zod'

export default {
    layout: MainAdmin,
    components: {
        Head, PageBreadcrumb
    },
    setup() {
        const toast = useToast();
        const currentPageTitle = ref('Assinaturas')
        const loading = ref(false);
        const routeToSave = ref(route("dash.admin.subscriptions.save"));
        const routeToSaveRenewalDetails = ref(route("dash.admin.subscriptionsrenewals.save"));

        const routeToGet = ref(route("api.dash.admin.subscriptions.get"));

        const routeToGetPlan = ref(route("api.dash.admin.subscriptions.plans.get"));
        const plansItems = ref([]);


        const routeToGetUsers = ref(route("api.dash.admin.users.get"));
        const usersItems = ref([]);

        const openNew = () => {

            submitted.value = false;
            itemDialog.value = true;
        };

        const getUsers = () => {
            axios.post(routeToGetUsers.value, {})
                .then((response) => {
                    if (!response.data.hasErrors) {
                        usersItems.value = response.data.items;
                    } else {
                        toast.add({ severity: "error", summary: "Erro!", detail: "Houve um erro tente novamente mais tarde.", life: 5000 })
                    }
                }).catch(function (error) {
                    toast.add({ severity: "error", summary: "Erro!", detail: "Houve um erro tente novamente mais tarde.", life: 5000 })
                })
        }

        const getPlans = () => {
            axios.post(routeToGetPlan.value, {})
                .then((response) => {
                    if (!response.data.hasErrors) {
                        plansItems.value = response.data.items;
                    } else {
                        toast.add({ severity: "error", summary: "Erro!", detail: "Houve um erro tente novamente mais tarde.", life: 5000 })
                    }
                }).catch(function (error) {
                    toast.add({ severity: "error", summary: "Erro!", detail: "Houve um erro tente novamente mais tarde.", life: 5000 })
                })
        }

        const getItems = () => {
            loading.value = true;
            axios.post(routeToGet.value, {})
                .then((response) => {

                    if (!response.data.hasErrors) {
                        items.value = response.data.items;
                        loading.value = false;
                        toast.add({ severity: "success", summary: "Sucesso!", detail: "Itens recarregados.", life: 5000 })
                    } else {
                        toast.add({ severity: "error", summary: "Erro!", detail: "Houve um erro tente novamente mais tarde.", life: 5000 })
                    }

                }).catch(function (error) {
                    toast.add({ severity: "error", summary: "Erro!", detail: "Houve um erro tente novamente mais tarde.", life: 5000 })
                })
        }

        onMounted(() => {
            getItems();
            getUsers();
            getPlans();
        });

        const itemDialog = ref(false);
        const submitted = ref(false);
        const deleteItemDialog = ref(false);
        const deleteItemsDialog = ref(false);

        const selectedItems = ref();

        const filters = ref({
            'global': { value: null, matchMode: FilterMatchMode.CONTAINS },
        });

        const items = ref([]);


        const item = ref({
            id: null,
            status: true
        });

        const hideDialog = () => {
            itemDialog.value = false;
            submitted.value = false;

            item.value = {
                id: null,
                status: true
            };
        };

        const editItem = (data) => {
            item.value = { ...data };

            itemDialog.value = true;
        };
        const findIndexById = (id) => {
            let index = -1;
            for (let i = 0; i < items.value.length; i++) {
                if (items.value[i].id === id) {
                    index = i;
                    break;
                }
            }

            return index;
        };

        const saveItem = ({ valid }) => {
            submitted.value = true;
            // loading.value = true;

            if (valid) {

                const itemToPost = { ...item.value }

                axios.post(routeToSave.value, { ...itemToPost, operation: 1 })
                    .then((response) => {

                        const data = response.data;

                        if (!data.hasErrors) {

                            item.value.created_at = data.item.created_at;
                            item.value.updated_at = data.item.updated_at;

                            // if (item.value.id) {
                            //     items.value[findIndexById(item.value.id)] = item.value;
                            // } else {
                            //     item.value.id = data.item.id
                            //     items.value.push(item.value);
                            // }


                            if (!item.value.id) {
                                item.value = data.item;

                                items.value.push(item.value);
                            }

                            const itemIndex = findIndexById(item.value.id);

                            items.value[itemIndex] = item.value;


                            let count = 0;

                            data.alerts.forEach((el) => {
                                toast.add({ severity: el.severity, summary: el.summary, detail: el.detail, life: 5000 + count });

                                count += 1000
                            })

                            hideDialog();

                        }
                    }).catch(function (error) {
                        toast.add({ severity: "error", summary: "Erro!", detail: error, life: 5000 })


                    })
            }
        };

        const saveRenewalItem = ({ valid }) => {


            if (valid) {

                const itemToPost = { ...itemDetailRenewals.value }

                const start_on = itemToPost.start_on;
                const end_on = itemToPost.end_on;
                const paid_at = itemToPost.paid_at;

                itemToPost.start_on = formatTimeStampToPost(start_on);


                if (itemToPost.end_on) {
                    itemToPost.end_on = formatTimeStampToPost(end_on);
                }

                if (itemToPost.paid_at) {
                    itemToPost.paid_at = formatTimeStampToPost(paid_at);
                }

                // DEBUG
                // console.log({ ...itemToPost, operation: 1 })
                // return true;
                // DEBUG

                axios.post(routeToSaveRenewalDetails.value, { ...itemToPost, operation: 1 })
                    .then((response) => {

                        const data = response.data;

                        if (!data.hasErrors) {

                            itemDetailRenewals.value.created_at = data.item.created_at;
                            itemDetailRenewals.value.updated_at = data.item.updated_at;

                            // if (itemDetailRenewals.value.id) {
                            //     itemsDetailRenewals.value[findIndexById(itemDetailRenewals.value.id)] = itemDetailRenewals.value;
                            // }
                            // else {
                            //     itemDetailRenewals.value.id = data.item.id
                            //     itemsDetailRenewals.value.push(itemDetailRenewals.value);
                            // }
                            
                            if (!itemDetailRenewals.value.id) {
                                itemDetailRenewals.value = data.item;

                                itemsDetailRenewals.value.push(itemDetailRenewals.value);
                            }

                            const itemIndex = findIndexById(itemDetailRenewals.value.id);

                            itemsDetailRenewals.value[itemIndex] = itemDetailRenewals.value;

                            let count = 0;

                            data.alerts.forEach((el) => {
                                toast.add({ severity: el.severity, summary: el.summary, detail: el.detail, life: 5000 + count });

                                count += 1000
                            })

                            closeRenewalDetailsDialog();

                        }
                    }).catch(function (error) {
                        toast.add({ severity: "error", summary: "Erro!", detail: error, life: 5000 })

                    })
            }
        };

        const getStatusLabel = (status) => {
            switch (status) {
                case true:
                    return 'success';
                default:
                    return 'danger'
            }
        };

        const resolver = ref(zodResolver(
            z.object({
                user_id: z.number({ required_error: 'O usuário é obrigatório', invalid_type_error: "O usuário é obrigatório", }).int(),
            })
        ))

        const formatTimeStampToPost = (date) => {
            const day = String(date.getDate()).padStart(2, '0');
            const month = String(date.getMonth() + 1).padStart(2, '0');
            const year = date.getFullYear();
            const hours = String(date.getHours()).padStart(2, '0');
            const minutes = String(date.getMinutes()).padStart(2, '0');

            return `${year}-${month}-${day} ${hours}:${minutes}:00`;
        }

        const formatDateToTable = (date) => {
            const day = String(date.getDate()).padStart(2, '0');
            const month = String(date.getMonth() + 1).padStart(2, '0');
            const year = date.getFullYear();

            return `${day}/${month}/${year}`;
        }

        const renewalsDialog = ref(false);

        const openRenewals = (id) => {
            renewalsDialog.value = true;

            item.value.id = id

            const subs = items.value.filter((el) => el.id === id)[0];

            itemsDetailRenewals.value = subs.renewals
        }

        const closeRenewals = () => {
            renewalsDialog.value = false;

            itemsDetailRenewals.value = []

            item.value.id = null
        }

        const itemsDetailRenewals = ref([]);

        const itemDetailRenewals = ref({
            id: null,
            start_on: null,
            price: "",
            status: true
        });

        const renewalDetailsDialog = ref(false);

        const resolverDetailRenewal = ref(zodResolver(
            z.object({
                plan_id: z.number({ required_error: 'O plano é obrigatório', invalid_type_error: "O plano é obrigatório", }).int(),
                price: z.string({
                    required_error: "O preço é obrigatório.",
                    invalid_type_error: "O preço é inválido!",
                }).min(1, { message: 'O preço é obrigatório' }),
                start_on: z.date({
                    required_error: "O início é obrigatório.",
                    invalid_type_error: "A data é inválida!",
                }),

            })
        ))

        var optionsPrice = {
            onComplete: function (price) {

            },
            onKeyPress: function (price, event, currentField, options) {

            },
            onChange: function (price) {
                itemDetailRenewals.value.price = price;
                console.log(price)
            },
            onInvalid: function (val, e, f, invalid, options) {

            },
            reverse: true
        };

        const openRenewalDetailsDialog = () => {
            renewalDetailsDialog.value = true;

            itemDetailRenewals.value.subscription_id = item.value.id

            setTimeout(() => {
                $('#price').mask('000.000.000.000.000,00', optionsPrice);
            }, 500)
        }

        const editRenewalDetailsDialog = (data) => {

            openRenewalDetailsDialog()

            itemDetailRenewals.value = { ...data };
            itemDetailRenewals.value.start_on = new Date(data.start_on)

            if (data.end_on) {
                itemDetailRenewals.value.end_on = new Date(data.end_on)
            }

            if (data.paid_at) {
                itemDetailRenewals.value.paid_at = new Date(data.paid_at)
            }

        }

        const closeRenewalDetailsDialog = () => {
            renewalDetailsDialog.value = false;

            itemDetailRenewals.value = {
                id: null,
                start_on: null,
                price: "",
                status: true
            }
        }

        const updatePlanFields = (id) => {
            const plan = plansItems.value.filter((el) => el.id === id)[0];

            itemDetailRenewals.value.price = plan.price;

            // Ao selecionar qualquer plano, o inicio vai ser na data atual
            itemDetailRenewals.value.start_on = new Date();

            // Se o plano tiver valor no days, vai ser calculado quando vai ser o fim.
            if (plan.days) {
                const date = new Date();
                date.setDate(date.getDate() + plan.days);
                itemDetailRenewals.value.end_on = date;
            }


        }

        return {
            formatDateToTable,
            editRenewalDetailsDialog,
            itemsDetailRenewals,
            updatePlanFields,
            itemDetailRenewals,
            resolverDetailRenewal,
            renewalDetailsDialog,
            openRenewalDetailsDialog,
            closeRenewalDetailsDialog,
            openRenewals,
            closeRenewals,
            renewalsDialog,
            resolver,
            currentPageTitle,
            openNew,
            items,
            item,
            selectedItems,
            itemDialog,
            submitted,
            getItems,
            filters,
            usersItems,
            hideDialog,
            saveItem,
            getStatusLabel,
            editItem,
            plansItems,
            saveRenewalItem
        }
    }
}

</script>

<template>

    <Head>
        <title>{{ currentPageTitle }}</title>
        <meta name="description" content="Descrição da Página">
    </Head>
    <PageBreadcrumb :pageTitle="currentPageTitle" />

    <Toast />

    <Dialog v-on:hide="closeRenewalDetailsDialog" :dismissableMask="true" v-model:visible="renewalDetailsDialog"
        header="Detalhe da Renovação" class="z-9999999" :modal="true">

        <Form ref="formRef" v-slot="$form" :resolver="resolverDetailRenewal" :initialValues="itemDetailRenewals"
            @submit="saveRenewalItem" class="flex flex-col justify-center gap-4">
            <div class="flex flex-col gap-6 w-[400px]">
                <div class="col-span-6">
                    <label for="plan_id" class="block font-bold mb-3">Plano</label>
                    <Select @change="updatePlanFields($event.value)" placeholder="Escolha um Plano" name="plan_id"
                        filter v-model="itemDetailRenewals.plan_id" :options="plansItems" optionLabel="name"
                        optionValue="id" class="w-full" />
                    <Message v-if="$form.plan_id?.invalid" severity="error" size="small" variant="simple">
                        {{ $form.plan_id.error?.message }}
                    </Message>
                </div>
                <div class="grid grid-cols-12 gap-4">
                    <div class="col-span-6">

                        <label for="price" class="block font-bold mb-3">Preço</label>

                        <InputGroup v-tooltip="'0 para plano grátis.'">
                            <InputGroupAddon>R$</InputGroupAddon>
                            <InputText placeholder="14,99" id="price" name="price"
                                v-model.trim="itemDetailRenewals.price" fluid />
                        </InputGroup>

                        <Message v-if="$form.price?.invalid" severity="error" size="small" variant="simple">{{
                            $form.price.error?.message }}</Message>
                    </div>
                    <div class="col-span-6">
                        <label for="paid_at" class="block font-bold mb-3">Pago em</label>
                        <DatePicker showTime hourFormat="24" step-minute="5" v-on:update:model-value="updateStartOn"
                            :manual-input="true" id="paid_at" v-model="itemDetailRenewals.paid_at" dateFormat="dd/mm/yy"
                            class="w-full" />

                    </div>
                </div>

                <div class="grid grid-cols-12 gap-4">
                    <div class="col-span-6">
                        <label for="start_on" class="block font-bold mb-3">Inicia em</label>
                        <DatePicker name="start_on" v-on:update:model-value="updateStartOn" :manual-input="true"
                            id="start_on" v-model="itemDetailRenewals.start_on" dateFormat="dd/mm/yy" class="w-full" />
                        <Message v-if="$form.start_on?.invalid" severity="error" size="small" variant="simple">{{
                            $form.start_on.error?.message }}</Message>
                    </div>
                    <div class="col-span-6">
                        <label for="end_on" class="block font-bold mb-3">Termina em</label>
                        <DatePicker name="end_on" v-model="itemDetailRenewals.end_on" dateFormat="dd/mm/yy"
                            class="w-full" />

                    </div>
                </div>


                <div class="grid grid-cols-12 gap-4">
                    <div class="col-span-6">
                        <label class="">
                            <span class="block font-bold mb-4">Ativo?</span>
                            <ToggleSwitch name="status" v-model="itemDetailRenewals.status" />
                        </label>
                    </div>

                </div>
                <!-- <div>
                        <label class="">
                            <span class="block font-bold mb-4">Ativo?</span>
                            <ToggleSwitch name="status" v-model="item.status" />
                        </label>
                    </div> -->
            </div>
            <div class="flex gap-4 ms-auto">
                <Button label="Cancelar" icon="pi pi-times" text @click="closeRenewalDetailsDialog" />
                <Button label="Salvar" type="submit" icon="pi pi-check" />
            </div>
        </Form>


    </Dialog>

    <Dialog :style="{ width: '70vw' }" v-on:hide="closeRenewals" :dismissableMask="true"
        v-model:visible="renewalsDialog" header="Renovações" class="z-9999999" :modal="true">

        <div class="overflow-hidden border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03] min-h-[400px]">
            <Toolbar class="mb-6">
                <template #start>
                    <Button label="Novo" icon="pi pi-plus" class="mr-2" @click="openRenewalDetailsDialog" />
                    <!-- <Button label="Delete" icon="pi pi-trash" severity="danger" outlined @click="confirmDeleteSelected"
                    :disabled="!selectedItems || !selecteditems.length" /> -->
                </template>

                <template #end>
                    <!-- <FileUpload mode="basic" accept="image/*" :maxFileSize="1000000" label="Import" customUpload
                    chooseLabel="Import" class="mr-2" auto :chooseButtonProps="{ severity: 'secondary' }" />
                <Button label="Export" icon="pi pi-upload" severity="secondary" @click="exportCSV($event)" /> -->
                    <Button label="Recarregar" icon="pi pi-refresh" severity="secondary" @click="getItems" />
                </template>
            </Toolbar>
            <DataTable ref="dt" v-model:selection="selectedItems" :value="itemsDetailRenewals" dataKey="id"
                :paginator="true" :rows="$page.props.defaultEntries" :filters="filters"
                paginatorTemplate="FirstPageLink PrevPageLink PageLinks NextPageLink LastPageLink CurrentPageReport RowsPerPageDropdown"
                :rowsPerPageOptions="$page.props.maxEntries"
                currentPageReportTemplate="Mostrando {first} até {last} de {totalRecords} itens" :loading="loading">
                <template #header>
                    <div class="flex flex-wrap gap-2 items-center justify-between">
                        <h4 class="m-0">Gerenciar Assinaturas</h4>
                        <IconField>
                            <InputIcon>
                                <i class="pi pi-search" />
                            </InputIcon>
                            <InputText v-model="filters['global'].value" placeholder="Procurar..." />
                        </IconField>
                    </div>
                </template>

                <Column selectionMode="multiple" style="width: 3rem" :exportable="false"></Column>

                <Column field="plan.name" header="Plano" sortable style="min-width: 16rem"></Column>

                <Column field="start_on" header="Início" sortable dataType="date" style="min-width: 10rem">
                    <template #body="slotProps">
                        {{ formatDateToTable(new Date(slotProps.data.start_on)) }}
                    </template>
                </Column>
                <Column field="end_on" header="Fim" sortable dataType="date" style="min-width: 10rem">
                    <template #body="slotProps">
                        {{ slotProps.data.end_on ? formatDateToTable(new Date(slotProps.data.end_on)) : "" }}
                    </template>
                </Column>

                <Column field="status" header="Status" sortable style="min-width: 12rem">
                    <template #body="slotProps">
                        <Tag :value="slotProps.data.status ? 'Ativo' : 'Inativo'"
                            :severity="getStatusLabel(slotProps.data.status)" />
                    </template>
                </Column>
                <Column field="updated_at" header="Atualizado em" sortable dataType="date" style="min-width: 10rem">


                </Column>
                <Column field="created_at" header="Criado em" sortable dataType="date" style="min-width: 10rem">

                </Column>
                <Column header="Ações" :exportable="false" style="min-width: 12rem">
                    <template #body="slotProps">
                        <Button icon="pi pi-pencil" outlined rounded class="mr-2"
                            @click="editRenewalDetailsDialog(slotProps.data)" />

                    </template>
                </Column>
            </DataTable>

        </div>

    </Dialog>

    <Dialog v-on:hide="hideDialog" :dismissableMask="true" v-model:visible="itemDialog" header="Detalhes da Assinatura"
        class="z-9999999" :modal="true">

        <Form ref="formRef" v-slot="$form" :resolver="resolver" :initialValues="item" @submit="saveItem"
            class="flex flex-col justify-center gap-4">

            <div class="flex gap-4 2xsm:flex-col lg:flex-row">
                <div class="flex flex-col gap-6 w-[400px]">

                    <div>
                        <label for="user_id" class="block font-bold mb-3">Usuário Contratante:</label>
                        <Select id="user_id" name="user_id" filter :invalid="submitted && !item.user_id"
                            v-model="item.user_id" :options="usersItems" optionLabel="name" optionValue="id"
                            class="w-full" />

                        <Message v-if="$form.user_id?.invalid" severity="error" size="small" variant="simple">{{
                            $form.user_id.error?.message }}</Message>
                    </div>

                    <div>
                        <label class="">
                            <span class="block font-bold mb-4">Ativo?</span>
                            <ToggleSwitch v-model="item.status" />
                        </label>
                    </div>

                </div>

            </div>

            <div class="flex gap-4 ms-auto">
                <Button label="Cancelar" icon="pi pi-times" text @click="hideDialog" />
                <Button label="Salvar" type="submit" icon="pi pi-check" />
            </div>

        </Form>


    </Dialog>


    <div class="overflow-hidden border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03] min-h-[400px]">
        <Toolbar class="mb-6">
            <template #start>
                <Button label="Novo" icon="pi pi-plus" class="mr-2" @click="openNew" />
                <!-- <Button label="Delete" icon="pi pi-trash" severity="danger" outlined @click="confirmDeleteSelected"
                    :disabled="!selectedItems || !selecteditems.length" /> -->
            </template>

            <template #end>
                <!-- <FileUpload mode="basic" accept="image/*" :maxFileSize="1000000" label="Import" customUpload
                    chooseLabel="Import" class="mr-2" auto :chooseButtonProps="{ severity: 'secondary' }" />
                <Button label="Export" icon="pi pi-upload" severity="secondary" @click="exportCSV($event)" /> -->
                <Button label="Recarregar" icon="pi pi-refresh" severity="secondary" @click="getItems" />
            </template>
        </Toolbar>
        <DataTable ref="dt" v-model:selection="selectedItems" :value="items" dataKey="id" :paginator="true"
            :rows="$page.props.defaultEntries" :filters="filters"
            paginatorTemplate="FirstPageLink PrevPageLink PageLinks NextPageLink LastPageLink CurrentPageReport RowsPerPageDropdown"
            :rowsPerPageOptions="$page.props.maxEntries"
            currentPageReportTemplate="Mostrando {first} até {last} de {totalRecords} itens" :loading="loading">
            <template #header>
                <div class="flex flex-wrap gap-2 items-center justify-between">
                    <h4 class="m-0">Gerenciar Assinaturas</h4>
                    <IconField>
                        <InputIcon>
                            <i class="pi pi-search" />
                        </InputIcon>
                        <InputText v-model="filters['global'].value" placeholder="Procurar..." />
                    </IconField>
                </div>
            </template>

            <Column selectionMode="multiple" style="width: 3rem" :exportable="false"></Column>

            <Column field="name" header="Contratante" sortable style="min-width: 16rem"></Column>

            <Column field="status" header="Status" sortable style="min-width: 12rem">
                <template #body="slotProps">
                    <Tag :value="slotProps.data.status ? 'Ativo' : 'Inativo'"
                        :severity="getStatusLabel(slotProps.data.status)" />
                </template>
            </Column>
            <Column field="updated_at" header="Atualizado em" sortable dataType="date" style="min-width: 10rem">


            </Column>
            <Column field="created_at" header="Criado em" sortable dataType="date" style="min-width: 10rem">

            </Column>
            <Column header="Ações" :exportable="false" style="min-width: 12rem">
                <template #body="slotProps">
                    <Button icon="pi pi-pencil" outlined rounded class="mr-2" @click="editItem(slotProps.data)" />
                    <Button v-tooltip="'Renovações'" icon="pi pi-refresh" outlined rounded class="mr-2"
                        @click="openRenewals(slotProps.data.id)" />
                </template>
            </Column>
        </DataTable>

    </div>
</template>