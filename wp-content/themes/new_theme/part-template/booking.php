<?php



/**



 Template Name: Boooking



 */





get_header(); ?>
  <!-- Content -->
  <?php if ( have_rows( 'banner' ) ) : ?>
	<?php while ( have_rows( 'banner' ) ) : the_row(); ?>
  <div class="content-wrapper">
    <!-- Lines -->
    <section class="content-lines-wrapper">
        <div class="content-lines-inner">
            <div class="content-lines"></div>
        </div>
    </section>
    <!-- Header Banner -->
    <section class="banner-header banner-img valign bg-img bg-fixed"
      style="background-position: center;"
      data-overlay-darkgray="5"
      data-background="<?php the_sub_field( 'background' ); ?>"></section>
      <!-- Blog  -->
    <section class="bauen-blog3 section-padding2">
			<div class="container">
				<div class="row">
					<div class="col-md-12">
						<br>
						<h2 class="section-title"><?php the_sub_field( 'title_page' ); ?></h2>
          </div>
				</div>
				<div class="row">
					<div class="col-md-12">
            <form class="static-booking-form row">
              <div class="form-group col-md-12">
                <label for="kodePromo">Kode Promo</label>
                <input type="text" id="kodePromo" aria-describedby="kodePromoHelp">
                <small id="kodePromoHelp" class="form-text text-muted">Kosongkan saja jika tidak memiliki kode promo.</small>
              </div>
              <div class="form-group col-md-12">
                <label for="namaLengkap">Nama Lengkap <span class="required">*</span></label>
                <input type="text" id="namaLengkap" aria-describedby="namaLengkapHelp">
              </div>
              <div class="form-group col-md-6">
                <label for="email">Email <span class="required">*</span></label>
                <input type="text" id="email" aria-describedby="emailHelp">
              </div>
              <div class="form-group col-md-6">
                <label for="nomorTelepon">Nomor Telepon</label>
                <input type="text" id="nomorTelepon" aria-describedby="nomorTeleponHelp">
              </div>
              <div class="form-group col-md-12">
                <label for="alamatProject">Alamat Project <span class="required">*</span></label>
                <textarea id="alamatProject" aria-describedby="alamatProjectHelp"></textarea>
              </div>
              <div class="form-group col-md-12">
                <label for="luasTanah">Luas Tanah <span class="required">*</span></label>
                <input type="text" id="luasTanah" aria-describedby="luasTanahHelp">
              </div>
              <div class="form-group col-md-12">
                <label for="luasBangunan">Luas Bangunan Saat Ini [Eksisting] <span class="required">*</span></label>
                <input type="text" id="luasBangunan" aria-describedby="luasBangunanHelp">
              </div>
              <div class="form-group col-md-6">
                <label for="fotoEksisting">Foto Saat Ini [Eksisting] <span class="required">*</span></label>
                <input type="file" id="fotoEksisting" aria-describedby="fotoEksistingHelp">
              </div>
              <div class="form-group col-md-6">
                <label for="referensiDesain">Referensi Desain <span class="required">*</span></label>
                <input type="file" id="referensiDesain" aria-describedby="referensiDesainHelp">
              </div>
              <div class="form-group col-md-12">
                <label for="permasalahanKebutuhan">Permasalahan & Kebutuhan Desain</label>
                <textarea id="permasalahanKebutuhan" aria-describedby="permasalahanKebutuhanHelp"></textarea>
              </div>
              <div class="form-group col-md-12">
                <label for="budgetPembangunan">Budget Pembangunan <span class="required">*</span></label>
                <input type="text" id="budgetPembangunan" aria-describedby="budgetPembangunanHelp">
              </div>

              <div class="col-md-12 d-flex justify-content-end">
                <button type="submit" class="btn">Booking Sekarang!</button>
              </div>
            </form>
				  </div>
        </div>
      </section>
  <?php endwhile; ?>
  <?php endif; ?>

  <script>
    document.querySelector('.static-booking-form').addEventListener('submit', async (e) => {
      e.preventDefault();
      
      const formData = {
        kode_promo: document.getElementById('kodePromo').value,
        nama_lengkap: document.getElementById('namaLengkap').value,
        email: document.getElementById('email').value, 
        nomor_telepon: document.getElementById('nomorTelepon').value,
        alamat_project: document.getElementById('alamatProject').value,
        luas_tanah: document.getElementById('luasTanah').value,
        luas_bangunan: document.getElementById('luasBangunan').value,
        foto_eksisting: document.getElementById('fotoEksisting').files[0],
        referensi_desain: document.getElementById('referensiDesain').files[0],
        permasalahan_kebutuhan: document.getElementById('permasalahanKebutuhan').value,
        budget_pembangunan: document.getElementById('budgetPembangunan').value
      };

      try {
        const response = await fetch('https://mailgun-webhook.interlook.co.id/send-email', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json'
          },
          body: JSON.stringify(formData)
        });

        if (response.ok) {
          // window.location.href = '/booking-success';
        } else {
          throw new Error('Failed to submit form');
        }
      } catch (error) {
        console.error('Error:', error);
      }
    });
  </script>

<?php get_footer();?>