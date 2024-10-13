<template>
    <div v-if="referral && referral.data">
        <v-heading :size="6" class="font-medium">{{ referral.data.recipient_user.name }}</v-heading>

        <div class="flex items-center gap-2 italic">
            <v-paragraph>{{ referral.data.recipient_user.email }}</v-paragraph>

            <template v-if="referral.data.recipient_user.phone_number">
                <div class="w-1 h-1 rounded-full bg-gray-500"></div>
                <v-paragraph>{{ referral.data.recipient_user.phone_number }}</v-paragraph>
            </template>
        </div>

        <v-paragraph class="italic">
            {{ address }}
        </v-paragraph>
    </div>
</template>

<script>
export default {
    props: {
        referral: {
            required: true,
        },
    },
    computed: {
        address() {
            return [
                this.referral.data.recipient_user.address_line_1,
                this.referral.data.recipient_user.address_line_2,
                this.referral.data.recipient_user.city,
                this.referral.data.recipient_user.state.name,
                this.referral.data.recipient_user.zip_code,
            ]
            .filter((item) => (typeof item === 'string' && item.length))
            .reduce((carry, item) => (`${carry}, ${item}`));
        },
    },
};
</script>