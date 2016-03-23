<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Behat\Context\Ui\Admin;

use Behat\Behat\Context\Context;
use PhpSpec\ObjectBehavior;
use Sylius\Behat\Context\Ui\Admin\ManagingTaxCategoryContext;
use Sylius\Behat\Page\Admin\Crud\IndexPageInterface;
use Sylius\Behat\Page\Admin\TaxCategory\CreatePageInterface;
use Sylius\Behat\Page\Admin\TaxCategory\UpdatePageInterface;
use Sylius\Behat\Service\Accessor\NotificationAccessorInterface;
use Sylius\Component\Core\Test\Services\SharedStorageInterface;
use Sylius\Component\Taxation\Model\TaxCategoryInterface;

/**
 * @mixin ManagingTaxCategoryContext
 *
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 */
class ManagingTaxCategoryContextSpec extends ObjectBehavior
{
    function let(
        SharedStorageInterface $sharedStorage,
        IndexPageInterface $taxCategoryIndexPage,
        CreatePageInterface $taxCategoryCreatePage,
        UpdatePageInterface $taxCategoryUpdatePage,
        NotificationAccessorInterface $notificationAccessor
    ) {
        $this->beConstructedWith(
            $sharedStorage,
            $taxCategoryIndexPage,
            $taxCategoryCreatePage,
            $taxCategoryUpdatePage,
            $notificationAccessor
        );
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Behat\Context\Ui\Admin\ManagingTaxCategoryContext');
    }

    function it_implements_context_interface()
    {
        $this->shouldImplement(Context::class);
    }

    function it_deletes_a_tax_cateogory(
        IndexPageInterface $taxCategoryIndexPage,
        SharedStorageInterface $sharedStorage,
        TaxCategoryInterface $taxCategory
    ) {
        $taxCategory->getCode()->willReturn('alcohol');

        $taxCategoryIndexPage->deleteResourceOnPage(['code' => 'alcohol'])->shouldBeCalled();
        $taxCategoryIndexPage->open()->shouldBeCalled();
        $sharedStorage->set('tax_category', $taxCategory)->shouldBeCalled();

        $this->iDeletedTaxCategory($taxCategory);
    }

    function it_checks_if_a_tax_category_does_not_exist_in_the_registry_anymore(
        TaxCategoryInterface $taxCategory,
        IndexPageInterface $taxCategoryIndexPage
    ) {
        $taxCategory->getCode()->willReturn('alcohol');
        $taxCategoryIndexPage->isResourceOnPage(['code' => 'alcohol'])->willReturn(false);

        $this->thisTaxCategoryShouldNoLongerExistInTheRegistry($taxCategory);
    }

    function it_throws_an_exception_if_a_tax_category_still_exist_in_the_registry(
        TaxCategoryInterface $taxCategory,
        IndexPageInterface $taxCategoryIndexPage
    ) {
        $taxCategory->getCode()->willReturn('alcohol');
        $taxCategoryIndexPage->isResourceOnPage(['code' => 'alcohol'])->willReturn(true);

        $this
            ->shouldThrow(new \InvalidArgumentException("Tax category with code alcohol exists but should not"))
            ->during('thisTaxCategoryShouldNoLongerExistInTheRegistry', [$taxCategory])
        ;
    }

    function it_checks_if_a_resource_was_successfully_deleted(NotificationAccessorInterface $notificationAccessor)
    {
        $notificationAccessor->hasSuccessMessage()->willReturn(true);
        $notificationAccessor->isSuccessfullyDeletedFor('tax_category')->willReturn(true);

        $this->iShouldBeNotifiedAboutSuccessfulDeletion();
    }

    function it_throws_an_exception_if_the_page_does_not_have_success_message(NotificationAccessorInterface $notificationAccessor)
    {
        $notificationAccessor->hasSuccessMessage()->willReturn(false);

        $this->shouldThrow(new \InvalidArgumentException('Message type is not positive'))->during('iShouldBeNotifiedAboutSuccessfulDeletion', []);
    }

    function it_throws_an_exception_if_the_message_on_a_page_is_not_related_to_deletion(NotificationAccessorInterface $notificationAccessor)
    {
        $notificationAccessor->hasSuccessMessage()->willReturn(true);
        $notificationAccessor->isSuccessfullyDeletedFor('tax_category')->willReturn(false);

        $this->shouldThrow(new \InvalidArgumentException('Successful deletion message does not appear'))->during('iShouldBeNotifiedAboutSuccessfulDeletion', []);
    }

    function it_opens_a_create_page(CreatePageInterface $taxCategoryCreatePage)
    {
        $taxCategoryCreatePage->open()->shouldBeCalled();

        $this->iWantToCreateNewTaxCategory();
    }

    function it_specifies_tax_category_code(CreatePageInterface $taxCategoryCreatePage)
    {
        $taxCategoryCreatePage->specifyCode('food_and_beverage')->shouldBeCalled();

        $this->iSpecifyItsCodeAs('food_and_beverage');
    }

    function it_specifies_tax_category_name(CreatePageInterface $taxCategoryCreatePage)
    {
        $taxCategoryCreatePage->nameIt('Food and Beverage')->shouldBeCalled();

        $this->iNameIt('Food and Beverage');
    }

    function it_specifies_tax_category_description(CreatePageInterface $taxCategoryCreatePage)
    {
        $taxCategoryCreatePage->describeItAs('Best stuff to get wasted in town')->shouldBeCalled();

        $this->iDescribeItAs('Best stuff to get wasted in town');
    }

    function it_creates_a_resource(CreatePageInterface $taxCategoryCreatePage)
    {
        $taxCategoryCreatePage->create()->shouldBeCalled();

        $this->iAddIt();
    }

    function it_asserts_if_a_resource_was_successfully_created(
        UpdatePageInterface $taxCategoryUpdatePage,
        TaxCategoryInterface $taxCategory
    ) {
        $taxCategoryUpdatePage->isOpen()->shouldBeCalled();
        $taxCategoryUpdatePage->hasResourceValues([
            'name' => 'Food and Beverage',
            'code'=> 'food_and_beverage',
            'description' => 'Best stuff to get wasted in town',
        ])->willReturn(true);

        $taxCategory->getCode()->willReturn('food_and_beverage');
        $taxCategory->getName()->willReturn('Food and Beverage');
        $taxCategory->getDescription()->willReturn('Best stuff to get wasted in town');

        $this->thisTaxCategoryShouldAppearInTheRegistry($taxCategory);
    }

    function it_throws_an_exception_if_resource_does_not_have_proper_fields_filled(
        UpdatePageInterface $taxCategoryUpdatePage,
        TaxCategoryInterface $taxCategory
    ) {
        $taxCategoryUpdatePage->isOpen()->shouldBeCalled();
        $taxCategoryUpdatePage->hasResourceValues([
            'name' => 'Food and Beverage',
            'code'=> 'food_and_beverage',
            'description' => 'Best stuff to get wasted in town',
        ])->willReturn(false);

        $taxCategory->getCode()->willReturn('food_and_beverage');
        $taxCategory->getName()->willReturn('Food and Beverage');
        $taxCategory->getDescription()->willReturn('Best stuff to get wasted in town');

        $this
            ->shouldThrow(new \InvalidArgumentException('Tax category with code food_and_beverage was found, but fields are not assigned properly'))
            ->during('thisTaxCategoryShouldAppearInTheRegistry', [$taxCategory])
        ;
    }

    function it_checks_if_a_resource_was_successfully_created(NotificationAccessorInterface $notificationAccessor)
    {
        $notificationAccessor->hasSuccessMessage()->willReturn(true);
        $notificationAccessor->isSuccessfullyCreatedFor('tax_category')->willReturn(true);

        $this->iShouldBeNotifiedAboutSuccessfulCreation();
    }

    function it_throws_an_exception_if_the_creation_page_does_not_have_success_message(NotificationAccessorInterface $notificationAccessor)
    {
        $notificationAccessor->hasSuccessMessage()->willReturn(false);

        $this
            ->shouldThrow(new \InvalidArgumentException('Message type is not positive'))
            ->during('iShouldBeNotifiedAboutSuccessfulCreation', [])
        ;
    }

    function it_throws_an_exception_if_the_message_on_a_page_is_not_related_to_creation(NotificationAccessorInterface $notificationAccessor)
    {
        $notificationAccessor->hasSuccessMessage()->willReturn(true);
        $notificationAccessor->isSuccessfullyCreatedFor('tax_category')->willReturn(false);

        $this
            ->shouldThrow(new \InvalidArgumentException('Successful creation message does not appear'))
            ->during('iShouldBeNotifiedAboutSuccessfulCreation', [])
        ;
    }

    function it_opens_an_update_page(UpdatePageInterface $taxCategoryUpdatePage, TaxCategoryInterface $taxCategory)
    {
        $taxCategory->getId()->willReturn(1);
        $taxCategoryUpdatePage->open(['id' => 1])->shouldBeCalled();

        $this->iWantToModifyNewTaxCategory($taxCategory);
    }

    function it_checks_if_the_code_cannot_be_changed(UpdatePageInterface $taxCategoryUpdatePage)
    {
        $taxCategoryUpdatePage->isCodeDisabled()->willReturn(true);

        $this->theCodeFieldShouldBeDisabled();
    }

    function it_throws_an_exception_if_the_code_field_is_not_immutable(UpdatePageInterface $taxCategoryUpdatePage)
    {
        $taxCategoryUpdatePage->isCodeDisabled()->willReturn(false);

        $this
            ->shouldThrow(new \InvalidArgumentException('Code should be immutable, but it does not'))
            ->during('theCodeFieldShouldBeDisabled')
        ;
    }

    function it_saves_changes(UpdatePageInterface $taxCategoryUpdatePage)
    {
        $taxCategoryUpdatePage->saveChanges()->shouldBeCalled();

        $this->iSaveMyChanges();
    }
}
