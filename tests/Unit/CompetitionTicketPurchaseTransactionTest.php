<?php

use App\Invirtu\InvirtuClient;
use App\Models\CompetitionTicketPurchase;
use App\Models\CompetitionTicketPurchaseTransaction;
use App\Models\CompetitionTicketType;
use App\Models\Event;
use App\Models\Recording;
use Tests\TestCase;

class CompetitionTicketPurchaseTransactionTest extends TestCase {

    public function testCreation() {

        $faker = \Faker\Factory::create();

        $purchase = CompetitionTicketPurchase::factory()->create();

        $data = array(
            'purchase_id' => $purchase->id,
            'payment_processor' => rand(0,6),
            'payment_or_refund' => rand(1,2),
    
        );

        $purchase = new CompetitionTicketPurchaseTransaction();

        $tmp_field = $data['purchase_id'];
        unset($data['purchase_id']);
        $this->assertFalse($purchase->validate($data));
        $data['purchase_id'] = $tmp_field;

        $tmp_field = $data['payment_processor'];
        unset($data['payment_processor']);
        $this->assertFalse($purchase->validate($data));
        $data['payment_processor'] = $tmp_field;

        $tmp_field = $data['payment_or_refund'];
        unset($data['payment_or_refund']);
        $this->assertFalse($purchase->validate($data));
        $data['payment_or_refund'] = $tmp_field;

        $this->assertTrue($purchase->validate($data));

        $purchase = new CompetitionTicketPurchaseTransaction();
        $purchase->forceFill($data);
        $purchase->save();

        $this->assertNotNull($purchase->id);

        //Reload with data from the observer
        $purchase->refresh();

    }

}