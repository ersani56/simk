<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up()
    {
        // Trigger untuk INSERT
        DB::unprepared('
            CREATE TRIGGER after_detail_insert
            AFTER INSERT ON pesanan_details
            FOR EACH ROW
            BEGIN
                UPDATE pesanans
                SET total_tagihan = (
                    SELECT COALESCE(SUM(harga * jumlah), 0)
                    FROM pesanan_details
                    WHERE no_faktur = NEW.no_faktur
                )
                WHERE no_faktur = NEW.no_faktur;
            END
        ');

        // Trigger untuk UPDATE
        DB::unprepared('
            CREATE TRIGGER after_detail_update
            AFTER UPDATE ON pesanan_details
            FOR EACH ROW
            BEGIN
                UPDATE pesanans
                SET total_tagihan = (
                    SELECT COALESCE(SUM(harga * jumlah), 0)
                    FROM pesanan_details
                    WHERE no_faktur = NEW.no_faktur
                )
                WHERE no_faktur = NEW.no_faktur;
            END
        ');

        // Trigger untuk DELETE
        DB::unprepared('
            CREATE TRIGGER after_detail_delete
            AFTER DELETE ON pesanan_details
            FOR EACH ROW
            BEGIN
                UPDATE pesanans
                SET total_tagihan = (
                    SELECT COALESCE(SUM(harga * jumlah), 0)
                    FROM pesanan_details
                    WHERE no_faktur = OLD.no_faktur
                )
                WHERE no_faktur = OLD.no_faktur;
            END
        ');
    }

    public function down()
    {
        DB::unprepared('DROP TRIGGER IF EXISTS after_detail_insert');
        DB::unprepared('DROP TRIGGER IF EXISTS after_detail_update');
        DB::unprepared('DROP TRIGGER IF EXISTS after_detail_delete');
    }
};
