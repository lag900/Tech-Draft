import { mount } from '@vue/test-utils';
import { describe, it, expect } from 'vitest';
import BaseButton from '@/components/UI/BaseButton.vue';

describe('BaseButton.vue', () => {
  it('renders default button correctly', () => {
    const wrapper = mount(BaseButton, {
      slots: {
        default: 'Click Me',
      },
    });

    expect(wrapper.text()).toBe('Click Me');
    expect(wrapper.classes()).toContain('bg-gradient-to-b'); // primary variant class
    expect(wrapper.classes()).toContain('text-sm'); // md size class
  });

  it('applies variant and size props', () => {
    const wrapper = mount(BaseButton, {
      props: {
        variant: 'danger',
        size: 'lg',
      },
      slots: {
        default: 'Delete',
      },
    });

    expect(wrapper.classes()).toContain('bg-red-600'); // danger variant class
    expect(wrapper.classes()).toContain('text-base'); // lg size class
  });

  it('handles disabled state', () => {
    const wrapper = mount(BaseButton, {
      props: {
        disabled: true,
      },
    });

    expect(wrapper.attributes('disabled')).toBeDefined();
  });

  it('shows loading spinner when loading prop is true', () => {
    const wrapper = mount(BaseButton, {
      props: {
        loading: true,
      },
    });

    expect(wrapper.find('svg.animate-spin').exists()).toBe(true);
    expect(wrapper.attributes('disabled')).toBeDefined();
  });

  it('emits click event when not disabled/loading', async () => {
    const wrapper = mount(BaseButton);
    await wrapper.trigger('click');
    expect(wrapper.emitted()).toHaveProperty('click');
  });
});
