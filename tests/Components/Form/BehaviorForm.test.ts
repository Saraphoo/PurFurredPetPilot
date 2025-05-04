import { describe, it, expect, beforeEach, vi } from 'vitest';
import { mount } from '@vue/test-utils';
import { createTestingPinia } from '@pinia/testing';
import BehaviorForm from '@/components/form/BehaviorForm.vue';
import { useForm } from '@inertiajs/vue3';

// Mock Inertia's useForm
vi.mock('@inertiajs/vue3', () => ({
    useForm: vi.fn(() => ({
        post: vi.fn(),
        processing: false,
        errors: {}
    }))
}));

describe('BehaviorForm', () => {
    let wrapper: any;

    beforeEach(() => {
        wrapper = mount(BehaviorForm, {
            props: {
                petId: 1,
                initialData: {
                    behaviors: ['Biting', 'Kicking', 'Spinning'],
                    behavior_notes: 'Behavior notes',
                    general_notes: 'General notes'
                }
            },
            global: {
                plugins: [createTestingPinia()]
            }
        });
    });

    it('renders the form with initial data', () => {
        expect(wrapper.find('form').exists()).toBe(true);
        expect(wrapper.find('.v-card-title').text()).toBe('Regular Behaviors');
    });

    it('selects and deselects behaviors', async () => {
        const checkboxes = wrapper.findAll('input[type="checkbox"]');
        
        // Select a behavior
        await checkboxes[0].setValue(true);
        expect(wrapper.vm.selectedBehaviors).toContain('Biting');
        
        // Deselect a behavior
        await checkboxes[0].setValue(false);
        expect(wrapper.vm.selectedBehaviors).not.toContain('Biting');
    });

    it('submits the form with correct data', async () => {
        const form = wrapper.find('form');
        await form.trigger('submit.prevent');

        expect(useForm).toHaveBeenCalledWith({
            pet_id: 1,
            behaviors: ['Biting', 'Kicking', 'Spinning'],
            behavior_notes: 'Behavior notes',
            general_notes: 'General notes'
        });
    });

    it('validates required fields', async () => {
        // Clear all behaviors
        await wrapper.setData({
            selectedBehaviors: []
        });

        const form = wrapper.find('form');
        await form.trigger('submit.prevent');

        // Check if validation errors are shown
        expect(wrapper.findAll('.v-messages__message').length).toBeGreaterThan(0);
    });

    it('resets the form after successful submission', async () => {
        const form = wrapper.find('form');
        await form.trigger('submit.prevent');

        // Mock the onSuccess callback
        const resetSpy = vi.spyOn(wrapper.vm, 'reset');
        await wrapper.vm.reset();

        expect(resetSpy).toHaveBeenCalled();
        expect(wrapper.vm.selectedBehaviors).toEqual([]);
        expect(wrapper.vm.behaviorNotes).toBe('');
        expect(wrapper.vm.generalNotes).toBe('');
    });

    it('updates behavior notes', async () => {
        const textarea = wrapper.find('textarea[label="Behavior Notes"]');
        await textarea.setValue('New behavior notes');
        
        expect(wrapper.vm.behaviorNotes).toBe('New behavior notes');
    });

    it('updates general notes', async () => {
        const textarea = wrapper.find('textarea[label="Notes about pet\'s behaviors"]');
        await textarea.setValue('New general notes');
        
        expect(wrapper.vm.generalNotes).toBe('New general notes');
    });
}); 