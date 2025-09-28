@extends('baseF')


@section('content')
<section id="featured-books" class="py-5 my-5">
    <div class="container">
        <div class="row">
            <div class="col-md-12">

                <div class="section-header align-center">
                    
                    <h2 class="section-title"> Cart</h2>
                </div>

                <div class="product-list" data-aos="fade-up">
                    <div class="row"></div>
<div class="card">
    <div class="row">
        <!-- Cart items -->
        <div class="col-md-8 cart">
            <div class="cartTitle">
                <div class="row">
                    <h2 class="section-titleCart">Shopping Cart</h2>
                    <div class="col align-self-center text-right text-muted">{{ $cartItems->count() }} items</div>
                </div>
            </div>

            @foreach($cartItems as $item)
                <div class="row border-top border-bottom">
                    <div class="row main align-items-center">
                        <div class="col-2">
                            <img class="img-fluid" src="{{ asset('storage/' . $item->livre->photo_couverture) }}">
                        </div>
                        <div class="col">
                            <div class="row text-muted">{{ $item->livre->auteur }}</div>
                            <div class="row">{{ $item->livre->titre }}</div>
                        </div>
                        <div class="col">
                            <!-- Boutons - et + -->
                            <form action="{{ route('cart.update', $item->id) }}" method="POST" style="display:inline;">
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="quantite" value="{{ $item->quantite - 1 }}">
                                <button type="submit" class="btn btn-outline-accent2 btn-accent-arrow">-</button>
                                
                            </form>

                            <a href="#" class="border px-2">{{ $item->quantite }}</a>

                            <form action="{{ route('cart.update', $item->id) }}" method="POST" style="display:inline;">
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="quantite" value="{{ $item->quantite + 1 }}">
                                <button type="submit" class="btn btn-outline-accent2 btn-accent-arrow">+</button>
                            </form>
                        </div>
                        <div class="col">
                            {{ $item->livre->prix * $item->quantite }} DT
                            <form action="{{ route('cart.remove', $item->id) }}" method="POST" style="display:inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="close" style="border:none;background:none;">&#10005;</button>
                            </form>
                        </div>
                    </div>
                </div>
            @endforeach

            <div class="back-to-shop">
                <a href="{{ route('livresf') }}">&leftarrow;</a>
                <span class="col align-self-center text-right text-muted">Back to shop</span>
            </div>
        </div>

        <!-- Summary -->
        <div class="col-md-4 summary">
            <h2><span class="col align-self-center text-center text-muted">Summary</span></h2>
            <hr>
            <div class="row">
                <div class="col" style="padding-left:0;">ITEMS {{ $cartItems->sum('quantite') }}</div>
                <div class="col text-right">{{ $cartItems->sum(fn($i) => $i->livre->prix * $i->quantite) }} DT</div>
            </div>
           
            <div class="row" style="border-top: 1px solid rgba(0,0,0,.1); padding: 2vh 0;">
                <div class="col">TOTAL PRICE</div>
                <div class="col text-right">
                    {{ $cartItems->sum(fn($i) => $i->livre->prix * $i->quantite) + 5 }} DT
                </div>
            </div>

       <form action="{{ route('checkout.form') }}" method="GET">
    @csrf
    <input type="hidden" name="montant" value="{{ $cartItems->sum(fn($i) => $i->livre->prix * $i->quantite) + 5 }}"> 
    <button class="btn btn-outline-accent btn-accent-arrow">PAY WITH PAYPAL</button>
</form>



        </div>
    </div>
</div>
 </div>
                </div>

            </div>
        </div>
    </div>
</section>
<style>
  
.cartTitle{
    margin-bottom: 5vh;
    font-family: "Raleway", arial, sans-serif;
}
.card{
    background: #dbd5d5ff;
    margin: auto;
    width: 90%;
    height: 50%;
    box-shadow: 0 6px 20px 0 rgba(0, 0, 0, 0.19);
    border-radius: 1rem;
    border: transparent;
}
@media(max-width:767px){
    .card{
        margin: 3vh auto;
    }
}
.cart{
    background-color: #e4d4c711;
    padding: 4vh 5vh;
    border-bottom-left-radius: 1rem;
    border-top-left-radius: 1rem;
}
@media(max-width:767px){
    .cart{
        padding: 4vh;
        border-bottom-left-radius: unset;
        border-top-right-radius: 1rem;
    }
}
.summary{
    background-color: #ddd;
    border-top-right-radius: 1rem;
    border-bottom-right-radius: 1rem;
    padding: 4vh;
    color: rgb(65, 65, 65);
}
@media(max-width:767px){
    .summary{
    border-top-right-radius: unset;
    border-bottom-left-radius: 1rem;
    }
}
.summary .col-2{
    padding: 0;
}
.summary .col-10
{
    padding: 0;
}.row{
    margin: 0;
}
.cartTitle b{
    font-size: 1.5rem;
}
.main{
    margin: 0;
    padding: 2vh 0;
    width: 100%;
}
.col-2, .col{
    padding: 0 1vh;
}
a{
    padding: 0 1vh;
}
.close{
    margin-left: auto;
    font-size: 0.7rem;
}
img{
    width: 3.5rem;
}
.back-to-shop{
    margin-top: 4.5rem;
}
h5{
    margin-top: 4vh;
}
hr{
    margin-top: 1.25rem;
}
form{
    padding: 2vh 0;
}

.btn{
    background-color: #e1b8b8ff;
    border-color: #dfa1a1ff;
    color: white;
    width: 100%;
    font-size: 0.7rem;
    margin-top: 4vh;
    padding: 1vh;
    border-radius: 0;
}
.btn:focus{
    box-shadow: none;
    outline: none;
    box-shadow: none;
    color: white;
    -webkit-box-shadow: none;
    -webkit-user-select: none;
    transition: none; 
}
.btn:hover{
    color: white;
}
.btn.btn-outline-accent:hover {
  border-color: #57553fd6;
  color: var(--dark-color) !important;
    background-color: #57553f8a;
}
.btn.btn-outline-accent2 {
    font-size: large;
  border-color: #57553fd6;
  color: var(--dark-color) !important;
    background-color: #9a988584;
}
.btn.btn-outline-accent2:hover {
  border-color: #57553fd6;
  color: var(--dark-color) !important;
    background-color: #57553f8a;
}
</style>
@endsection
