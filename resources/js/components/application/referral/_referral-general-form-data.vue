<template>
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6" v-if="referral && referral.data">
        <div>
            <v-heading :size="6" class="font-medium">Referral Reasons</v-heading>

            <div class="grid gap-2">
                <template v-for="(referralReason, referralReasonIndex) in referral.data.referral_reasons.map((referralReason) => referralReason.name)" :key="'referralReason_' + referralReasonIndex">
                    <div>
                        <v-badge>{{ referralReason }}</v-badge>
                    </div>
                </template>
            </div>
        </div>

        <div>
            <v-heading :size="6" class="font-medium">Procedure</v-heading>
            <template v-if="referral.data.procedure">
                <v-badge>{{ referral.data.procedure.name }}</v-badge>
            </template>

            <template v-else>
                <v-form-error>No procedure selected.</v-form-error>
            </template>
        </div>

        <div>
            <v-heading :size="6" class="font-medium">Notes</v-heading>
            <template v-if="referral.data.notes">
                <v-paragraph>{{ referral.data.notes }}</v-paragraph>
            </template>

            <template v-else>
                <v-form-error>No notes available.</v-form-error>
            </template>
        </div>
    </div>
</template>

<script>
export default {
    props: {
        referral: {
            required: true,
        },
    },
};
</script>